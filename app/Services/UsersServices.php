<?php

namespace App\Services;

use App\Domain\Entities\UserEntity;
use App\Domain\Repositories\UserRepositoryInterface;;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;
use DateTime;
use Exception;
use Gemini;
use Illuminate\Support\Facades\Log;

class UsersServices
{

    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function login(array $data)
    {

        $user = $this->getUserWithEmail($data['email']);
        if(!empty($user) && Hash::check($data['password'], $user->getPassword())){
            $exp = time() + 3600;
            $token = JWT::encode(
                [
                    'iss' => "user-manager",           // Emissor do token
                    'iat' => time(),                 // Emitido em
                    'exp' => $exp,                  // Expira em 1 hora
                    'user_id' => $user->getUuid(), // Dados do usuário
                ],
                env('JWTKEY'),
                'HS256'
            );
            return [
                'token' => $token,
                'exp' => $exp
            ];
        }

        throw new Exception("Credentials invalid", 400);
    }

    public function createUser(array $data)
    {
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        $user = new UserEntity(
            Uuid::uuid4()->toString(),
            $data['name'],
            $data['email'],
            $password,
            $data['phone'],
            $data['is_admin'],
            $data['role'],
            new DateTime(),
            new DateTime()
        );
        $user = $this->userRepository->createUser($user->toArray());
        if($user){
            $user = $user->toArray();
            unset($user['password']);
            unset($user['id']);
            return $user;
        }

        throw new Exception("User not created", 503);
    }

    public function getAllUsers()
    {
       $data = $this->userRepository->getAllUsers();
       if ($data) {
            $list = [];
            foreach ($data as $user){
                $user = $this->userRepository->modelToEntity($user);
                $user = $user->toArray();

                unset($user['password']);

                $list[] = $user;
            }
            return $list;
       }

       throw new Exception("Not content users", 204);

    }

    public function getUserWithEmail(string $email)
    {
       $user = $this->userRepository->getUserWithEmail($email);
       if ($user) {
            return $user;
       }

       throw new Exception("User not found", 400);

    }

    public function getUserWithUuid(string $uuid)
    {
       $user = $this->userRepository->getUserWithUuid($uuid);
       if ($user) {
            return $user;
       }

       throw new Exception("User not found", 400);

    }

    public function changeRoleUser(string $uuid, array $data)
    {
        $user = $this->getUserWithUuid($uuid);
        if($user){
            // New role
            $user->changeRole($data['role']);
            $this->userRepository->updateRole($user);
            $user =$user->toArray();

            unset($user['password']);
            return $user;


        }

        throw new Exception("User not found", 400);
    }

    public function webhookMessage(array $payload)
    {
        $event = $payload['event'];
        Log::info("EVENT", ['event' => $event]);

        if(!empty($payload['payload']['body'])){

            $user = $payload['payload']['_data']['notifyName'];
            $message = $payload['payload']['body'];
            Log::info("API WHATS", ['user' => $user, 'message' => $message, 'payload' => $payload]);

            $gemini = Gemini::client(env('GEMINIKEY'));
            $message = "Verifique se essa mensagem contém coisas suspeitas, tanto no conteudo como no fromato, e retorne uma string json sem quebras de linhas com o campo its_okay com true se não tiver maliciosidade ou false se tiver maliciosidade além disso envie um campo message com o seu relaório resumido do conteúdo da mensagem: " . $message;
            $messageValidation = $gemini->geminiFlash()->generateContent($message);
            $return = json_decode($messageValidation->text(), true);
            Log::info("GEMINI RESPONSE", ['message' => $messageValidation->text()]);

        }
        return true;
    }
}
