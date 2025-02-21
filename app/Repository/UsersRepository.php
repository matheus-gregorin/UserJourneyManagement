<?php

namespace App\Repository;

use App\Models\UsersModel;

class UsersRepository
{
    private UsersModel $usersModel;

    public function __construct(UsersModel $usersModel)
    {
     $this->usersModel = $usersModel;   
    }

    public function get()
    {
        return $this->usersModel::all();
    }
}
