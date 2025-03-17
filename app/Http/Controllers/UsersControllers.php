<?php

namespace App\Http\Controllers;

use App\Http\Requests\changeRoleUserRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Services\UsersServices;
use Exception;
use Illuminate\Http\Request;

class UsersControllers extends Controller
{

    private UsersServices $usersServices;

    public function __construct(UsersServices $usersServices) {
        $this->usersServices = $usersServices;
    }

    public function login(LoginRequest $request)
    {
        try {
            $data = $this->usersServices->login($request->all());

            return response()->json([
                'success' => true,
                'message' => "User authenticated",
                'token' => $data['token'],
                'expire_id' => $data['exp']
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode());
        }
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
                'message' => $e->getMessage()
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
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    public function changeRoleUser(string $uuid, changeRoleUserRequest $request)
    {
        try {
            $user = $this->usersServices->changeRoleUser($uuid, $request->all());

            return response()->json([
                'success' => true,
                'message' => "User role updated",
                'data' => $user
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    public function webhookMessage(Request $request)
    {
        {
            try {
                $this->usersServices->webhookMessage($request->all());
    
                return response()->json([
                    'success' => true
                ], 200);
    
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 503);
            }
        }
    }
}
