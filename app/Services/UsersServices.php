<?php

namespace App\Services;

use App\Entitys\UserEntity;
use App\Repository\UsersRepository;
use DateTime;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class UsersServices
{

    private UsersRepository $usersRepository;

    public function __construct(UsersRepository $usersRepository) {
        $this->usersRepository = $usersRepository;
    }

    public function login(array $data)
    {

        $user = $this->getUser($data['email']);
        if(Hash::check($data['password'], $user->getPassword())){
            $exp = time() + 3600;
            $token = JWT::encode(
                [
                    'iss' => "user-manager",           // Emissor do token
                    'aud' => "user-manager",          // Destinatário
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
            $data['is_admin'],
            $data['role'],
            new DateTime(),
            new DateTime()
        );
        $user = $this->usersRepository->createUser($user->toArray());
        if($user){

        }
        unset($user['password']);

        return $user;
    }

    public function getAllUsers()
    {
       $data = $this->usersRepository->getAllUsers();
       if ($data) {
            $list = [];
            foreach ($data as $user){
                $user = $this->usersRepository->modelToEntity($user);
                $user = $user->toArray();

                unset($user['password']);

                $list[] = $user;
            }
            return $list;
       }

       throw new Exception("Not content users", 204);

    }

    public function getUser(string $email)
    {
       $user = $this->usersRepository->getUser($email);
       if ($user) {
            return $this->usersRepository->modelToEntity($user);
       }

       throw new Exception("User not found", 400);

    }
}
