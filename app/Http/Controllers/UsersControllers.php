<?php

namespace App\Http\Controllers;

use Domain\Enums\CodesEnum;
use App\Exceptions\CollectUserByPhoneException;
use App\Exceptions\CollectUserByUuidException;
use App\Exceptions\CompanyNotFoundException;
use App\Exceptions\CredentialsInvalidException;
use App\Exceptions\NotContentUsersException;
use App\Exceptions\RestartUserException;
use App\Exceptions\UpdateRoleException;
use App\Exceptions\UserNotCreatedException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserNotIsAdminException;
use App\Http\Requests\changeRoleUserRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Responses\ApiResponse;
use App\Services\UsersServices;
use App\UseCase\ChangeRoleUserUseCase;
use App\UseCase\CreateUserUseCase;
use App\UseCase\GetAllUsersUseCase;
use App\UseCase\LoginUseCase;
use App\UseCase\WebhookReceiveMessageWahaUseCase;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UsersControllers extends Controller
{
    private LoginUseCase $loginUseCase;
    private CreateUserUseCase $createUserUseCase;
    private GetAllUsersUseCase $getAllUsersUseCase;
    private ChangeRoleUserUseCase $changeRoleUserUseCase;
    private WebhookReceiveMessageWahaUseCase $webhookReceiveMessageWahaUseCase;
    private UsersServices $usersServices;

    public function __construct(
        LoginUseCase $loginUseCase,
        CreateUserUseCase $createUserUseCase,
        GetAllUsersUseCase $getAllUsersUseCase,
        ChangeRoleUserUseCase $changeRoleUserUseCase,
        WebhookReceiveMessageWahaUseCase $webhookReceiveMessageWahaUse,
        UsersServices $usersServices
    ) {
        $this->loginUseCase = $loginUseCase;
        $this->createUserUseCase = $createUserUseCase;
        $this->getAllUsersUseCase = $getAllUsersUseCase;
        $this->changeRoleUserUseCase = $changeRoleUserUseCase;
        $this->webhookReceiveMessageWahaUseCase = $webhookReceiveMessageWahaUse;
        $this->usersServices = $usersServices;
    }

    public function login(LoginRequest $request)
    {
        try {
            $data = $this->loginUseCase->login($request->all());

            return ApiResponse::success(
                [
                    'token' => $data['token'],
                    'expire_id' => $data['exp']
                ],
                CodesEnum::messageUserAuthenticated,
                CodesEnum::codeSuccess
            );
        } catch (CredentialsInvalidException $e) {
            return ApiResponse::error(
                [
                    CodesEnum::messageCredentialsInvalid
                ],
                CodesEnum::messageUserNotAuthenticated,
                CodesEnum::codeErrorUnauthorized
            );
        } catch (UserNotFoundException $e) {
            return ApiResponse::error(
                [
                    CodesEnum::messageCredentialsInvalid
                ],
                CodesEnum::messageUserNotAuthenticated,
                CodesEnum::codeErrorUnauthorized
            );
        } catch (UserNotIsAdminException $e) {
            return ApiResponse::error(
                [
                    CodesEnum::messageUserNotIsAdmin
                ],
                CodesEnum::messageUserNotAuthenticated,
                CodesEnum::codeErrorForbidden
            );
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    CodesEnum::messageInternalServerError
                ],
                CodesEnum::messageUserNotAuthenticated,
                CodesEnum::codeErrorInternalServerError
            );
        }
    }

    public function createUser(CreateUserRequest $request)
    {
        try {
            $user = $this->createUserUseCase->createUser($request->all());

            return ApiResponse::success(
                [
                    'user' => $user
                ],
                CodesEnum::messageUserCreated,
                CodesEnum::codeSuccess
            );
        } catch (UserNotCreatedException $e) {
            return ApiResponse::error(
                [
                    CodesEnum::messageDataInvalid
                ],
                CodesEnum::messageUserNotCreated,
                CodesEnum::codeErrorBadRequest
            );
        } catch (UserNotFoundException $e) {
            return ApiResponse::error(
                [
                    CodesEnum::messageInternalServerError
                ],
                CodesEnum::messageUserNotCreated,
                CodesEnum::codeErrorInternalServerError
            );
        } catch (CompanyNotFoundException $e) {
            return ApiResponse::error(
                [
                    CodesEnum::messageCompanyNotFound
                ],
                CodesEnum::messageUserNotCreated,
                CodesEnum::codeErrorBadRequest
            );
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    CodesEnum::messageInternalServerError
                ],
                CodesEnum::messageUserNotCreated,
                CodesEnum::codeErrorInternalServerError
            );
        }
    }

    public function getAllUsers()
    {
        try {
            $users = $this->getAllUsersUseCase->getAllUsers();

            return ApiResponse::success(
                [
                    'users' => $users
                ],
                CodesEnum::messageUsersCollected,
                CodesEnum::codeSuccess
            );
        } catch (NotContentUsersException $e) {
            return ApiResponse::error(
                [
                    CodesEnum::messageUsersNotContent
                ],
                CodesEnum::messageUsersNotContent,
                CodesEnum::codeNotContent
            );
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    CodesEnum::messageInternalServerError
                ],
                CodesEnum::messageUserNotCreated,
                CodesEnum::codeErrorInternalServerError
            );
        }
    }

    public function changeRoleUser(string $uuid, changeRoleUserRequest $request)
    {
        try {
            $user = $this->changeRoleUserUseCase->changeRoleUser($uuid, $request->all());

            return ApiResponse::success(
                [
                    'user' => $user
                ],
                CodesEnum::messageRoleUpdated,
                CodesEnum::codeSuccess
            );
        } catch (UserNotFoundException $e) {
            return ApiResponse::error(
                [
                    CodesEnum::messageUserInvalid
                ],
                CodesEnum::messageNotUpdatedRole,
                CodesEnum::codeErrorBadRequest
            );
        } catch (CollectUserByUuidException $e) {
            return ApiResponse::error(
                [
                    CodesEnum::messageUserInvalid
                ],
                CodesEnum::messageNotUpdatedRole,
                CodesEnum::codeErrorBadRequest
            );
        } catch (UpdateRoleException $e) {
            return ApiResponse::error(
                [
                    CodesEnum::messageNotUpdatedRole
                ],
                CodesEnum::messageNotUpdatedRole,
                CodesEnum::codeErrorInternalServerError
            );
        } catch (Exception $e) {
            return ApiResponse::error(
                [
                    CodesEnum::messageInternalServerError
                ],
                CodesEnum::messageNotUpdatedRole,
                CodesEnum::codeErrorInternalServerError
            );
        }
    }

    public function webhookReceiveMessage(Request $request)
    { {
            try {
                $this->webhookReceiveMessageWahaUseCase->webhookReceiveMessage($request->all());

                return response()->json([
                    'success' => true
                ], 200);
            } catch (CollectUserByPhoneException $e) {
                Log::critical(CodesEnum::messageErrorGetUserByPhone , ['message' => $e->getMessage()]);
            } catch (Exception $e) {
                Log::critical(CodesEnum::messageErrorReceiveMessage, ['message' => $e->getMessage()]);
            }
        }
    }

    public function validateUsersOff(Request $request)
    {
        try {
            // Validate users off
            $this->usersServices->validateUsersOff();

            return ApiResponse::success(
                [],
                CodesEnum::automationDispatchSuccess,
                CodesEnum::codeSuccess
            );
        } catch (UserNotFoundException $e) {
            Log::critical("Error in get user with scope: ", ['message' => $e->getMessage()]);
            return ApiResponse::error(
                [
                    $e->getMessage()
                ],
                CodesEnum::automationDispatchError,
                CodesEnum::codeErrorBadRequest
            );
        } catch (CollectUserByPhoneException $e) {
            Log::critical(CodesEnum::messageErrorGetUserByPhone, ['message' => $e->getMessage()]);
            return ApiResponse::error(
                [
                    $e->getMessage()
                ],
                CodesEnum::automationDispatchError,
                CodesEnum::codeErrorBadRequest
            );
        } catch (RestartUserException $e) {
            Log::critical(CodesEnum::messageErrorUserRestartScope, ['message' => $e->getMessage()]);
            return ApiResponse::error(
                [
                    $e->getMessage()
                ],
                CodesEnum::automationDispatchError,
                CodesEnum::codeErrorInternalServerError
            );
        } catch (Exception $e) {
            Log::critical(CodesEnum::messageInternalServerError, ['message' => $e->getMessage()]);
            return ApiResponse::error(
                [
                    $e->getMessage()
                ],
                CodesEnum::automationDispatchError,
                CodesEnum::codeErrorInternalServerError
            );
        }
    }
}
