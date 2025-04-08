<?php

namespace App\Repositories;

use App\Domain\Entities\UserEntity;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Exceptions\CollectUserByPhoneException;
use App\Exceptions\UpdateOtpException;
use App\Exceptions\UserNotFoundException;
use App\Models\UserMongoDbModel;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class UserMongoDbRepository implements UserRepositoryInterface
{
    private UserMongoDbModel $UserMongoDbModel;

    public function __construct(UserMongoDbModel $UserMongoDbModel)
    {
     $this->UserMongoDbModel = $UserMongoDbModel;   
    }

    public function createUser(array $data): UserEntity|null|Exception
    {
        try {
            return $this->modelToEntity($this->UserMongoDbModel::create($data), true);

        } catch (Exception $e) {
            Log::critical("Error in created user: ", ['message' => $e->getMessage()]);
            throw new Exception("Error in created user", 400);

        }
    }

    public function getAllUsers(): Collection|null|Exception
    {
        try {
            return $this->UserMongoDbModel::all();

        } catch (Exception $e) {
            Log::critical("Error in get all users: ", ['message' => $e->getTraceAsString()]);
            throw new Exception("Error get all users", 400);

        }
    }

    public function getUserWithEmail(string $email): UserEntity|null|Exception
    {
        try {
            return $this->modelToEntity($this->UserMongoDbModel::where('email', '=', $email)->first());

        } catch (Exception $e) {
            Log::critical("Error in get user: ", ['message' => $e->getTraceAsString()]);
            throw new Exception("Error in get user: " . $e->getMessage(), 400);

        }
    }

    public function getUserWithUuid(string $uuid): UserEntity|null|Exception
    {
        try {
            return $this->modelToEntity($this->UserMongoDbModel::where('uuid', '=', $uuid)->first());

        } catch (Exception $e) {
            Log::critical("Error in get user: ", ['message' => $e->getTraceAsString()]);
            throw new Exception("Error in get user: " . $e->getMessage(), 400);

        }
    }

    public function getUserWithPhoneNumber(string $number): UserEntity|null|Exception
    {
        try {
            return $this->modelToEntity($this->UserMongoDbModel::where('phone', '=', $number)->first());

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
            $userModel = $this->UserMongoDbModel::where('uuid', $user->getUuid())->first();
            $userModel->role = $user->getRole();
            $userModel->save();

            return $user;

        } catch (Exception $e) {
            Log::critical("Error in update user: ", ['message' => $e->getTraceAsString()]);
            throw new Exception("Error in update user: " . $e->getMessage(), 400);

        }
    }

    public function updateOTP(UserEntity $user)
    {
        try {
            $userModel = $this->UserMongoDbModel::where('uuid', $user->getUuid())->first();
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
            $userModel = $this->UserMongoDbModel::where('uuid', $user->getUuid())->first();
            $userModel->is_auth = $user->getIsAuth();
            $userModel->save();

            return $user;

        } catch (Exception $e) {
            Log::critical("Error in update otp: ", ['message' => $e->getMessage()]);
            throw new UpdateOtpException("Error in update otp: " . $e->getMessage(), 400);

        }
    }

    public function modelToEntity($UserMongoDbModel, bool $removePass = false): UserEntity|Exception
    {

        if(is_null($UserMongoDbModel)){
            throw new Exception("User not found", 400);
        }

        $password = $removePass ? "" : $UserMongoDbModel->password;

        return new UserEntity(
            $UserMongoDbModel->uuid,
            $UserMongoDbModel->name,
            $UserMongoDbModel->email,
            $password,
            $UserMongoDbModel->is_auth,
            $UserMongoDbModel->otp,
            $UserMongoDbModel->phone,
            $UserMongoDbModel->is_admin,
            $UserMongoDbModel->role,
            $UserMongoDbModel->updated_at->toDateTime(),
            $UserMongoDbModel->created_at->toDateTime()
        );
    }
}
