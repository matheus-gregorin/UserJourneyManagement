<?php

namespace App\Http\Controllers;

use App\Http\Requests\changeRoleUserRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Services\UsersServices;
use App\UseCase\ChangeRoleUserUseCase;
use App\UseCase\CreateUserUseCase;
use App\UseCase\GetAllUsersUseCase;
use App\UseCase\LoginUseCase;
use App\UseCase\WebhookReceiveMessageWahaUseCase;
use Exception;
use Illuminate\Http\Request;

class UsersControllers extends Controller
{

    private UsersServices $usersServices;
    private LoginUseCase $loginUseCase;
    private CreateUserUseCase $createUserUseCase;
    private GetAllUsersUseCase $getAllUsersUseCase;
    private ChangeRoleUserUseCase $changeRoleUserUseCase;
    private WebhookReceiveMessageWahaUseCase $webhookReceiveMessageWahaUseCase;

    public function __construct(
        UsersServices $usersServices,
        LoginUseCase $loginUseCase,
        CreateUserUseCase $createUserUseCase,
        GetAllUsersUseCase $getAllUsersUseCase,
        ChangeRoleUserUseCase $changeRoleUserUseCase,
        WebhookReceiveMessageWahaUseCase $webhookReceiveMessageWahaUse
    )
    {
        $this->usersServices = $usersServices;
        $this->loginUseCase = $loginUseCase;
        $this->createUserUseCase = $createUserUseCase;
        $this->getAllUsersUseCase = $getAllUsersUseCase;
        $this->changeRoleUserUseCase = $changeRoleUserUseCase;
        $this->webhookReceiveMessageWahaUseCase = $webhookReceiveMessageWahaUse;
    }

    public function login(LoginRequest $request)
    {
        try {
            $data = $this->loginUseCase->login($request->all());

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
            $user = $this->createUserUseCase->createUser($request->all());

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
            $users = $this->getAllUsersUseCase->getAllUsers();

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
            $user = $this->changeRoleUserUseCase->changeRoleUser($uuid, $request->all());

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

    public function webhookReceiveMessage(Request $request)
    {
        {
            try {
                $this->webhookReceiveMessageWahaUseCase->webhookReceiveMessage($request->all());
    
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
