<?php

namespace App\Services;

use App\Entitys\UserEntity;
use App\Repository\UsersRepository;
use DateTime;
use Exception;
use Ramsey\Uuid\Uuid;

class UsersServices
{

    private UsersRepository $usersRepository;

    public function __construct(UsersRepository $usersRepository) {
        $this->usersRepository = $usersRepository;
    }

    public function login(array $data)
    {

        $user = $this->getUser($data['email']);
       if(password_verify($data['password'], $user->getPassword())){
            dd("Passou");
       }
       dd("Falhou");
    }

    public function createUser(array $data)
    {
        $password = password_hash($data['password'], PASSWORD_BCRYPT);
        $user = new UserEntity(
            Uuid::uuid4()->toString(),
            $data['name'],
            $data['email'],
            $password,
            $data['is_admin'],
            $data['role'],
            new DateTime(),
            new DateTime()
        );
        $user = $this->usersRepository->createUser($user->toArray());
        unset($user['password']);

        return $user;
    }

    public function getAllUsers()
    {
       $data = $this->usersRepository->getAllUsers();
       if ($data) {
            $list = [];
            foreach ($data as $user){
                $user = $this->usersRepository->modelToEntity($user);
                $user = $user->toArray();

                unset($user['password']);

                $list[] = $user;
            }
            return $list;
       }

       throw new Exception("Not content users", 204);

    }

    public function getUser(string $email)
    {
       $user = $this->usersRepository->getUser($email);
       if ($user) {
            return $this->usersRepository->modelToEntity($user);
       }

       throw new Exception("User not found", 400);

    }
}
