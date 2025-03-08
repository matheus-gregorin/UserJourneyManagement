<?php

namespace App\Repository;

interface UserRepositoryInterface
{
    public function createUser(array $data);
    public function getAllUsers();
    public function getUser(string $email);
}
