<?php

namespace App\Services;

use App\Repository\UsersRepository;

class UsersServices
{

    private UsersRepository $usersRepository;

    public function __construct(UsersRepository $usersRepository) {
        $this->usersRepository = $usersRepository;
    }

    public function get()
    {
       $data = $this->usersRepository->get();

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
