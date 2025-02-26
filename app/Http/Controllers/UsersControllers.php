<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Services\UsersServices;
use Illuminate\Http\Request;

class UsersControllers extends Controller
{

    private UsersServices $usersServices;

    public function __construct(UsersServices $usersServices) {
        $this->usersServices = $usersServices;
    }

    public function createUser(CreateUserRequest $request)
    {
        return $this->usersServices->createUser($request->all());
    }

    public function getAllUsers()
    {
        $this->usersServices->getAllUsers();
    }
}
