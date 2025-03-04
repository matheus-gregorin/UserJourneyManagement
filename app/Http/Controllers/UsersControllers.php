<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Services\UsersServices;
use Exception;
use Illuminate\Http\Request;

class UsersControllers extends Controller
{

    private UsersServices $usersServices;

    public function __construct(UsersServices $usersServices) {
        $this->usersServices = $usersServices;
    }

    public function createUser(CreateUserRequest $request)
    {
        try {

            $user = $this->usersServices->createUser($request->all());

            return response()->json([
                'success' => true,
                'message' => "User created",
                'data' => $user
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], $e->getCode());
        }
    }

    public function getAllUsers()
    {
        try {

            $users = $this->usersServices->getAllUsers();

            return response()->json([
                'success' => true,
                'message' => "Users collected",
                'data' => $users
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], $e->getCode());
        }
    }
}
