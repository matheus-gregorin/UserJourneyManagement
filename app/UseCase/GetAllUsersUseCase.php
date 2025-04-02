<?php

namespace App\UseCase;

use App\Domain\Repositories\UserRepositoryInterface;
use App\Exceptions\NotContentUsersException;
use Exception;
use Illuminate\Support\Facades\Log;

class GetAllUsersUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        $data = $this->userRepository->getAllUsers();

        $list = [];
        foreach ($data as $user){
            $user = $this->userRepository->modelToEntity($user);
            $user = $user->toArray();

            unset($user['password']);
            unset($user['otp_code']);

            $list[] = $user;
        }

        if(empty($list)){
            Log::critical("GetAllUsersUseCase invalid", ['data' => json_encode($data)]);
            throw new NotContentUsersException("Not content users", 204);
       }

        return $list;
    }
}
