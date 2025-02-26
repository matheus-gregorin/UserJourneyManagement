<?php

namespace App\Services;

use App\Repository\UsersRepository;

class UsersServices
{

    private UsersRepository $usersRepository;

    public function __construct(UsersRepository $usersRepository) {
        $this->usersRepository = $usersRepository;
    }

    public function createUser(array $data)
    {
        dd($data); // Criar entity e passar para bcrypt o password
        $this->usersRepository->createUser($data);
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
