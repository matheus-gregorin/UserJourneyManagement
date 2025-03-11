<?php

namespace App\Repository;

use App\Entitys\UserEntity;
use App\Models\UserMongoDbModel;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class UserMongoDbRepository implements UserRepositoryInterface
{
    private UserMongoDbModel $UserMongoDbModel;

    public function __construct(UserMongoDbModel $UserMongoDbModel)
    {
     $this->UserMongoDbModel = $UserMongoDbModel;   
    }

    public function createUser(array $data): UserEntity|null|Exception
    {
        try {
            return $this->modelToEntity($this->UserMongoDbModel::create($data), true);

        } catch (Exception $e) {
            Log::critical("Error in created user: ", ['message' => $e->getMessage()]);
            throw new Exception("Error in created user", 400);

        }
    }

    public function getAllUsers(): Collection|null|Exception
    {
        try {
            return $this->UserMongoDbModel::all();

        } catch (Exception $e) {
            Log::critical("Error in get all users: ", ['message' => $e->getTraceAsString()]);
            throw new Exception("Error get all users", 400);

        }
    }

    public function getUser(string $email): UserEntity|null|Exception
    {
        try {
            return $this->modelToEntity($this->UserMongoDbModel::where('email', '=', $email)->first());

        } catch (Exception $e) {
            Log::critical("Error in get user: ", ['message' => $e->getTraceAsString()]);
            throw new Exception("Error in get user: " . $e->getMessage(), 400);

        }
    }

    public function modelToEntity($UserMongoDbModel, bool $removePass = false): UserEntity|Exception
    {

        if(is_null($UserMongoDbModel)){
            throw new Exception("User not found", 400);
        }

        $password = $removePass ? "" : $UserMongoDbModel->password;

        return new UserEntity(
            $UserMongoDbModel->uuid,
            $UserMongoDbModel->name,
            $UserMongoDbModel->email,
            $password,
            $UserMongoDbModel->is_admin,
            $UserMongoDbModel->role,
            $UserMongoDbModel->updated_at->toDateTime(),
            $UserMongoDbModel->created_at->toDateTime()
        );
    }
}
