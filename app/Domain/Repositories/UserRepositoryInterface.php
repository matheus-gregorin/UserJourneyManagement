<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\UserEntity;
use Exception;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function createUser(array $data): UserEntity|null|Exception;

    public function getAllUsers(): Collection|null|Exception;

    public function getUserWithEmail(string $email): UserEntity|null|Exception;

    public function getUserWithUuid(string $uuid): UserEntity|null|Exception;

    public function updateRole(UserEntity $user): UserEntity|null|Exception;

    public function modelToEntity($userModel): UserEntity|Exception;
}
