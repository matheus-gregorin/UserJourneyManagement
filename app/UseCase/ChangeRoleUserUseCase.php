<?php

namespace App\UseCase;

use App\Domain\Repositories\UserRepositoryInterface;
use App\Exceptions\UserNotFoundException;
use Exception;

class ChangeRoleUserUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function changeRoleUser(string $uuid, array $data)
    {
        $user = $this->userRepository->getUserWithUuid($uuid);
        if($user){
            // New role
            $user->changeRole($data['role']);
            $this->userRepository->updateRole($user);
            $user =$user->toArray();

            unset($user['password']);
            return $user;


        }

        throw new UserNotFoundException("User not found", 400);
    }
}
