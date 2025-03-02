<?php

namespace App\Entitys;

use DateTime;
use Ramsey\Uuid\Uuid;

class UserEntity
{

    private string $uuid;

    private string $name;

    private string $email;

    private string $password;

    private bool $isAdmin;

    private string $role;

    private DateTime $createdAt;

    public function __construct(
        string $name,
        string $email,
        string $password,
        bool $isAdmin,
        string $role,
        DateTime $createdAt
    )
    {
        $this->uuid = Uuid::uuid4();
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->isAdmin = $isAdmin;
        $this->role = $role;
        $this->createdAt = $createdAt;
    }

    public function getUuid()
    {
        return $this->uuid;
    }
}
