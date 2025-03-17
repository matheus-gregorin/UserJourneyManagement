<?php

namespace App\Repositories;

use App\Domain\Entities\UserEntity;
use App\Domain\Repositories\UserRepositoryInterface;
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
            throw new Exception("Error in created user", 400);

        }
    }

    public function getAllUsers(): Collection|null|Exception
    {
        try {
            return $this->UserMysqlModel::all();

        } catch (Exception $e) {
            Log::critical("Error in get all users: ", ['message' => $e->getTraceAsString()]);
            throw new Exception("Error get all users", 400);

        }
    }

    public function getUserWithEmail(string $email): UserEntity|null|Exception
    {
        try {
            return $this->modelToEntity($this->UserMysqlModel::where('email', '=', $email)->first());

        } catch (Exception $e) {
            Log::critical("Error in get user: ", ['message' => $e->getTraceAsString()]);
            throw new Exception("Error in get user: " . $e->getMessage(), 400);

        }
    }

    public function getUserWithUuid(string $uuid): UserEntity|null|Exception
    {
        try {
            return $this->modelToEntity($this->UserMysqlModel::where('uuid', '=', $uuid)->first());

        } catch (Exception $e) {
            Log::critical("Error in get user: ", ['message' => $e->getTraceAsString()]);
            throw new Exception("Error in get user: " . $e->getMessage(), 400);

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
            Log::critical("Error in update user: ", ['message' => $e->getTraceAsString()]);
            throw new Exception("Error in update user: " . $e->getMessage(), 400);

        }
    }

    public function modelToEntity($UserMysqlModel, bool $removePass = false): UserEntity|Exception
    {

        if(is_null($UserMysqlModel)){
            throw new Exception("User not found", 400);
        }

        $password = $removePass ? "" : $UserMysqlModel->password;

        return new UserEntity(
            $UserMysqlModel->uuid,
            $UserMysqlModel->name,
            $UserMysqlModel->email,
            $password,
            $UserMysqlModel->phone,
            $UserMysqlModel->is_admin,
            $UserMysqlModel->role,
            $UserMysqlModel->updated_at,
            $UserMysqlModel->created_at
        );
    }
}
