<?php

namespace App\Services;

use Carbon\Carbon;
use Domain\Repositories\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class UsersServices
{

    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validateUsersOff()
    {
        $users = $this->userRepository->getUserWithContainsScopes();
        Log::info("Qtd Users off: " . count($users), []);
        if (!empty($users)) {
            $now = Carbon::now();
            foreach ($users as $key => $user) {
                $diffHour = $now->diffInHours($user->updated_at);
                Log::info("User off: " . $user->name, [
                    "updated_at" => $user->updated_at,
                    "hoursOff" => $diffHour . "h"
                ]);
                if ($diffHour >= 3) {
                    $this->restartUser($user->uuid, $user->phone);
                }
            }
            return true;
        }

        throw new Exception("Not contains users with scope");
    }

    public function restartUser(string $uuid, string $phone)
    {
        $user = $this->userRepository->getUserWithPhoneNumber($phone);

        sendMessageWhatsapp(
            $user->getPhone(),
            "notContains",
            [
                "😔 Parece que não temos notícias suas há algum tempo! Para garantir a segurança da sua conta, precisaremos reiniciar o seu processo de login. Quando estiver pronto, é só fazer login novamente para continuar de onde parou, ok? até mais!"
            ],
            0,
            false
        );

        $this->userRepository->restartUser($user);

        return true;
    }
}
