<?php

namespace App\Repository;

use App\Models\UsersModel;
use Exception;
use Illuminate\Support\Facades\Log;

class UsersRepository
{
    private UsersModel $usersModel;

    public function __construct(UsersModel $usersModel)
    {
     $this->usersModel = $usersModel;   
    }

    public function createUser()
    {
        try {

            $dados = [
                "id" => 12,
                "name" => "Math2",
                "email" => "math2@gmail.com",
                "password" => bcrypt("12345678"), // sempre criptografe a senha
                "is_admin" => false,
                "role" => json_encode(["low"]) // ou implode(',', ["low"])
            ];
            $user = UsersModel::create($dados);
            dd('Usuário criado com sucesso!', $user);

        } catch (Exception $e) {

            dd("Error in created user: ", ['message' => $e->getMessage()]);
            return [];

        }
    }

    public function getAllUsers()
    {
        try {

            return $this->usersModel::all();

        } catch (Exception $e) {

            Log::critical("Error in get all users: ", ['message' => $e->getTraceAsString()]);
            return [];

        }
    }
}
