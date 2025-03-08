<?php

namespace App\Repository;

use App\Entitys\UserEntity;
use App\Models\UserMysqlModel;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class UsersMysqlRepository implements UserRepositoryInterface
{
    private UserMysqlModel $UserMysqlModel;

    public function __construct(UserMysqlModel $UserMysqlModel)
    {
     $this->UserMysqlModel = $UserMysqlModel;   
    }

    public function createUser(array $data): UserMysqlModel|null|Exception
    {
        try {
            return $this->UserMysqlModel::create($data);

        } catch (Exception $e) {
            Log::critical("Error in created user: ", ['message' => $e->getMessage()]);
            throw new Exception("Error in created user", 400);

        }
    }

    public function getAllUsers(): Collection|null|Exception
    {
        try {
            return $this->UserMysqlModel::all();

        } catch (Exception $e) {
            Log::critical("Error in get all users: ", ['message' => $e->getTraceAsString()]);
            throw new Exception("Error get all users", 400);

        }
    }

    public function getUser(string $email): UserMysqlModel|null|Exception
    {
        try {
            return $this->UserMysqlModel::where('email', '=', $email)->first();

        } catch (Exception $e) {
            Log::critical("Error in get user: ", ['message' => $e->getTraceAsString()]);
            throw new Exception("Error get user", 400);

        }
    }

    public function modelToEntity($UserMysqlModel)
    {
        return new UserEntity(
            $UserMysqlModel->uuid,
            $UserMysqlModel->name,
            $UserMysqlModel->email,
            $UserMysqlModel->password,
            $UserMysqlModel->is_admin,
            $UserMysqlModel->role,
            $UserMysqlModel->updated_at,
            $UserMysqlModel->created_at
        );
    }
}
