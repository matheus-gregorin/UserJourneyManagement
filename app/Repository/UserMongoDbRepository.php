<?php

namespace App\Repository;

use App\Entitys\UserEntity;
use App\Models\UserMongoDbModel;
use Exception;

class UserMongoDbRepository implements UserRepositoryInterface
{
    private UserMongoDbModel $UserMongoDbModel;

    public function __construct(UserMongoDbModel $UserMongoDbModel)
    {
     $this->UserMongoDbModel = $UserMongoDbModel;   
    }

    public function createUser(array $data)
    {
        try {
            return $this->UserMongoDbModel::create($data);
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function getAllUsers()
    {
        return $this->UserMongoDbModel::all();
    }

    public function getUser(string $email)
    {

    }

    public function modelToEntity($UserMongoDbModel)
    {
        return new UserEntity(
            $UserMongoDbModel->uuid,
            $UserMongoDbModel->name,
            $UserMongoDbModel->email,
            $UserMongoDbModel->password,
            $UserMongoDbModel->is_admin,
            $UserMongoDbModel->role,
            $UserMongoDbModel->updated_at,
            $UserMongoDbModel->created_at
        );
    }
}
