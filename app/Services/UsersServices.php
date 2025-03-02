<?php

namespace App\Services;

use App\Entitys\UserEntity;
use App\Repository\UsersRepository;
use DateTime;

class UsersServices
{

    private UsersRepository $usersRepository;

    public function __construct(UsersRepository $usersRepository) {
        $this->usersRepository = $usersRepository;
    }

    public function createUser(array $data)
    {
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        $user = new UserEntity(
            $data['name'],
            $data['email'],
            $password,
            $data['is_admin'],
            $data['role'],
            new DateTime()
        );
        $this->usersRepository->createUser();
    }

    public function getAllUsers()
    {
       $data = $this->usersRepository->getAllUsers();
       if ($data) {
            $list = [];

            foreach ($data as $user){
                $list[$user->id] = [
                    "name" => $user->name,
                    "email" => $user->email,
                    "password" => $user->password,
                    "is_admin" => $user->is_admin,
                    "roles" => $user->roles,
                    "created_at" => $user->CREATED_AT
                ];
            }
    
            dd('Service', $list);
       }
    }
}
