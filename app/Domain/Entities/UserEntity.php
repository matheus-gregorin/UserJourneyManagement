<?php

namespace App\Domain\Entities;

use App\Domain\Enums\RolesEnum;
use DateTime;

class UserEntity
{

    private string $uuid;

    private string $name;

    private string $email;

    private string $password;

    private bool $isAdmin;

    private string $role;

    private DateTime $updatedAt;

    private DateTime $createdAt;

    public function __construct(
        string $uuid,
        string $name,
        string $email,
        string $password,
        bool $isAdmin,
        string $role,
        DateTime $updatedAt,
        DateTime $createdAt
    )
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->isAdmin = $isAdmin;
        $this->role = $this->enterPermission($role);
        $this->updatedAt = $updatedAt;
        $this->createdAt = $createdAt;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
        return $this;
    }

    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin)
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    private function setrole(string $role)
    {
        $this->role = $role;
        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    // Valida as permissões
    public function enterPermission(string $role): string
    {
        switch ($role) {
            case RolesEnum::LOW:
                    return RolesEnum::LOW;
                break;
            case RolesEnum::MEDIUM:
                    return RolesEnum::MEDIUM;
                break;
            case RolesEnum::HIGH:
                    return RolesEnum::HIGH;
                break;
            default:
                    return RolesEnum::LOW;
                break;
        }
    }

    // Muda as permissões
    public function changeRole(string $newRole)
    {
        switch ($newRole) {
            case RolesEnum::LOW:
                    if(in_array($this->getRole(), [RolesEnum::MEDIUM, RolesEnum::HIGH])){
                        $this->setrole(RolesEnum::LOW);
                    }
                break;
            case RolesEnum::MEDIUM:
                    if(in_array($this->getRole(), [RolesEnum::LOW, RolesEnum::HIGH])){
                        $this->setrole(RolesEnum::MEDIUM);
                    }
                break;
            case RolesEnum::HIGH:
                    if(in_array($this->getRole(), [RolesEnum::LOW, RolesEnum::MEDIUM])){
                        $this->setrole(RolesEnum::HIGH);
                    }
                    break;
            default:
                    $this->setrole(RolesEnum::LOW);
                break;
        }
    }

    public function toArray()
    {
        return [
            "uuid" => $this->getUuid(),
            "name" => $this->getName(),
            "email" => $this->getEmail(),
            "password" => $this->getPassword(),
            "is_admin" => $this->getIsAdmin(),
            "role" => $this->getRole(),
            "updated_at" => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
            "created_at" => $this->getCreatedAt()->format('Y-m-d H:i:s')
        ];
    }
}
