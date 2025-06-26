<?php

namespace App\UseCase;

use Domain\Repositories\UserRepositoryInterface;
use App\Exceptions\CredentialsInvalidException;
use App\Exceptions\UserNotIsAdminException;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(array $data)
    {

        $user = $this->userRepository->getUserWithEmail($data['email']);
        if (!empty($user)) {

            if(!$user->getIsAdmin()){
                throw new UserNotIsAdminException("User not is admin", 403);
            }

            if (Hash::check($data['password'], $user->getPassword())) {
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
        }

        Log::critical("LoginUseCase invalid", ['data' => json_encode($data)]);
        throw new CredentialsInvalidException("Credentials invalid", 400);
    }
}
