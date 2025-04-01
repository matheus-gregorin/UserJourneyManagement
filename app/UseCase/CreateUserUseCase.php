<?php

namespace App\UseCase;

use App\Domain\Entities\UserEntity;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Exceptions\UserNotCreatedException;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class CreateUserUseCase
{

    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function createUser(array $data)
    {
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        $user = new UserEntity(
            Uuid::uuid4()->toString(),
            $data['name'],
            $data['email'],
            $password,
            false,
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

        Log::critical("CreateUserUseCase invalid", ['data' => json_encode($data)]);
        throw new UserNotCreatedException("User not created", 503);
    }

}
