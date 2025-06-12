<?php

namespace App\Services;

use Domain\Repositories\UserRepositoryInterface;

class UsersServices
{

    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function get()
    {
        //
    }
}
