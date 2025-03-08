<?php

namespace App\Repository;

use App\Models\UserMongoDbModel;
use App\Models\UserMysqlModel;

interface UserRepositoryInterface
{
    public function createUser(array $data);
    public function getAllUsers();
    public function getUser(string $email);
    public function modelToEntity($userModel);
}
