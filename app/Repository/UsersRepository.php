<?php

namespace App\Repository;

use App\Entitys\UserEntity;
use App\Models\UsersModel;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class UsersRepository
{
    private UsersModel $usersModel;

    public function __construct(UsersModel $usersModel)
    {
     $this->usersModel = $usersModel;   
    }

    public function createUser(array $data): UsersModel|null|Exception
    {
        try {
            return UsersModel::create($data);

        } catch (Exception $e) {
            Log::critical("Error in created user: ", ['message' => $e->getMessage()]);
            throw new Exception("Error in created user", 400);

        }
    }

    public function getAllUsers(): Collection|null|Exception
    {
        try {
            return $this->usersModel::all();

        } catch (Exception $e) {
            Log::critical("Error in get all users: ", ['message' => $e->getTraceAsString()]);
            throw new Exception("Error get all users", 400);

        }
    }

    public function modelToEntity(UsersModel $userModel)
    {
        return new UserEntity(
            $userModel->uuid,
            $userModel->name,
            $userModel->email,
            $userModel->password,
            $userModel->is_admin,
            $userModel->role,
            $userModel->updated_at,
            $userModel->created_at
        );
    }
}
