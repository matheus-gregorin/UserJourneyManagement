<?php

namespace App\Repositories;

use App\Domain\Entities\UserEntity;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Exceptions\CollectUserByEmailException;
use App\Exceptions\CollectUserByPhoneException;
use App\Exceptions\CollectUserByUuidException;
use App\Exceptions\NotContentUsersException;
use App\Exceptions\UpdateOtpException;
use App\Exceptions\UpdateRoleException;
use App\Exceptions\UserNotCreatedException;
use App\Exceptions\UserNotFoundException;
use App\Models\UserMysqlModel;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class UsersMysqlRepository implements UserRepositoryInterface
{
    private UserMysqlModel $UserMysqlModel;

    public function __construct(UserMysqlModel $UserMysqlModel)
    {
     $this->UserMysqlModel = $UserMysqlModel;   
    }

    public function createUser(array $data): UserEntity|null|Exception
    {
        try {
            return $this->modelToEntity($this->UserMysqlModel::create($data), true);

        } catch (Exception $e) {
            Log::critical("Error in created user: ", ['message' => $e->getMessage()]);
            throw new UserNotCreatedException("Error in created user", 400);

        }
    }

    public function getAllUsers(): Collection|null|Exception
    {
        try {
            return $this->UserMysqlModel::all();

        } catch (Exception $e) {
            Log::critical("Error in get all users: ", ['message' => $e->getMessage()]);
            throw new NotContentUsersException("Error get all users", 400);

        }
    }

    public function getUserWithEmail(string $email): UserEntity|null|Exception
    {
        try {
            return $this->modelToEntity($this->UserMysqlModel::where('email', '=', $email)->first());

        } catch (UserNotFoundException $e) {
            Log::critical("Error in get user by email: ", ['message' => $e->getMessage()]);
            throw new UserNotFoundException($e->getMessage(), 400);

        } catch (Exception $e) {
            Log::critical("Error in get user by email: ", ['message' => $e->getMessage()]);
            throw new CollectUserByEmailException($e->getMessage(), 400);

        }
    }

    public function getUserWithUuid(string $uuid): UserEntity|null|Exception
    {
        try {
            return $this->modelToEntity($this->UserMysqlModel::where('uuid', '=', $uuid)->first());

        } catch (UserNotFoundException $e) {
            Log::critical("Error in get user by uuid: ", ['message' => $e->getMessage()]);
            throw new UserNotFoundException($e->getMessage(), 400);

        } catch (Exception $e) {
            Log::critical("Error in get user by uuid: ", ['message' => $e->getMessage()]);
            throw new CollectUserByUuidException($e->getMessage(), 400);

        }
    }

    public function getUserWithPhoneNumber(string $number): UserEntity|null|Exception
    {
        try {
            return $this->modelToEntity($this->UserMysqlModel::where('phone', '=', $number)->first());

        } catch (UserNotFoundException $e) {
            Log::critical("Error in get user by phone number: ", ['message' => $e->getMessage()]);
            throw new UserNotFoundException($e->getMessage(), 400);

        } catch (Exception $e) {
            Log::critical("Error in get user by phone number: ", ['message' => $e->getMessage()]);
            throw new CollectUserByPhoneException($e->getMessage(), 400);

        }
    }

    public function updateRole(UserEntity $user): UserEntity|null|Exception
    {
        try {
            $userModel = $this->UserMysqlModel::where('uuid', $user->getUuid())->first();
            $userModel->role = $user->getRole();
            $userModel->save();

            return $user;

        } catch (Exception $e) {
            Log::critical("Error in update user: ", ['message' => $e->getMessage()]);
            throw new UpdateRoleException("Error in update user: " . $e->getMessage(), 400);

        }
    }

    public function updateOTP(UserEntity $user)
    {
        try {
            $userModel = $this->UserMysqlModel::where('uuid', $user->getUuid())->first();
            $userModel->otp = $user->getOtpCode();
            $userModel->save();

            return $user;

        } catch (Exception $e) {
            Log::critical("Error in update otp: ", ['message' => $e->getMessage()]);
            throw new UpdateOtpException("Error in update otp: " . $e->getMessage(), 400);

        }
    }

    public function authUser(UserEntity $user)
    {
        try {
            $userModel = $this->UserMysqlModel::where('uuid', $user->getUuid())->first();
            $userModel->is_auth = $user->getIsAuth();
            $userModel->save();

            return $user;

        } catch (Exception $e) {
            Log::critical("Error in update otp: ", ['message' => $e->getMessage()]);
            throw new UpdateOtpException("Error in update otp: " . $e->getMessage(), 400);

        }
    }

    public function modelToEntity($UserMysqlModel, bool $removePass = false): UserEntity|Exception
    {

        if(is_null($UserMysqlModel)){
            Log::critical("User not found: ", ['user' => json_encode($UserMysqlModel)]);
            throw new UserNotFoundException("User not found", 400);
        }

        $password = $removePass ? "" : $UserMysqlModel->password;

        return new UserEntity(
            $UserMysqlModel->uuid,
            $UserMysqlModel->name,
            $UserMysqlModel->email,
            $password,
            $UserMysqlModel->is_auth,
            $UserMysqlModel->otp,
            $UserMysqlModel->phone,
            $UserMysqlModel->is_admin,
            $UserMysqlModel->role,
            $UserMysqlModel->updated_at,
            $UserMysqlModel->created_at
        );
    }
}
