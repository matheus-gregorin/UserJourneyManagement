<?php

namespace App\UseCase;

use App\Domain\Entities\UserEntity;
use App\Domain\Repositories\UserRepositoryInterface;
use DateTime;
use Exception;
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

}
