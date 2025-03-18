<?php

namespace App\UseCase;

use App\Domain\Repositories\UserRepositoryInterface;
use Exception;

class GetAllUsersUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
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
}
