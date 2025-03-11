<?php

namespace App\Repository;

use App\Entitys\UserEntity;
use Exception;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function createUser(array $data): UserEntity|null|Exception;

    public function getAllUsers(): Collection|null|Exception;

    public function getUser(string $email): UserEntity|null|Exception;

    public function modelToEntity($userModel): UserEntity|Exception;
}
