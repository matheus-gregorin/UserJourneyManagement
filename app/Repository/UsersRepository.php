<?php

namespace App\Repository;

use App\Entitys\UserEntity;
use App\Models\UserModel;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class UsersRepository implements UserRepositoryInterface
{
    private UserModel $UserModel;

    public function __construct(UserModel $UserModel)
    {
     $this->UserModel = $UserModel;   
    }

    public function createUser(array $data): UserModel|null|Exception
    {
        try {
            return $this->UserModel::create($data);

        } catch (Exception $e) {
            Log::critical("Error in created user: ", ['message' => $e->getMessage()]);
            throw new Exception("Error in created user", 400);

        }
    }

    public function getAllUsers(): Collection|null|Exception
    {
        try {
            return $this->UserModel::all();

        } catch (Exception $e) {
            Log::critical("Error in get all users: ", ['message' => $e->getTraceAsString()]);
            throw new Exception("Error get all users", 400);

        }
    }

    public function getUser(string $email): UserModel|null|Exception
    {
        try {
            return $this->UserModel::where('email', '=', $email)->first();

        } catch (Exception $e) {
            Log::critical("Error in get user: ", ['message' => $e->getTraceAsString()]);
            throw new Exception("Error get user", 400);

        }
    }

    public function modelToEntity(UserModel $userModel)
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
