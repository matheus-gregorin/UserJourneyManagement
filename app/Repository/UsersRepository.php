<?php

namespace App\Repository;

use App\Models\UsersModel;
use Exception;
use Illuminate\Support\Facades\Log;

class UsersRepository
{
    private UsersModel $usersModel;

    public function __construct(UsersModel $usersModel)
    {
     $this->usersModel = $usersModel;   
    }

    public function createUser(array $data)
    {
        try {

            return $this->usersModel::created($data);

        } catch (Exception $e) {

            Log::critical("Error in created user: ", ['message' => $e->getTraceAsString()]);
            return [];

        }
    }

    public function getAllUsers()
    {
        try {

            return $this->usersModel::all();

        } catch (Exception $e) {

            Log::critical("Error in get all users: ", ['message' => $e->getTraceAsString()]);
            return [];

        }
    }
}
