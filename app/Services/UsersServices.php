<?php

namespace App\Services;

use Carbon\Carbon;
use Domain\Entities\UserEntity;
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
        $users = $this->userRepository->getUserWithContainsScopesOrAuth();
        Log::info("Qtd Users off: " . count($users), []);
        if (!empty($users)) {
            $now = Carbon::now();
            foreach ($users as $key => $user) {
                $diffHour = $now->diffInHours($user->getUpdatedAt());
                Log::info("User off: " . $user->getName(), [
                    'phone' => $user->getPhone(),
                    "updated_at" => $user->getUpdatedAt(),
                    "hoursOff" => $diffHour . "h"
                ]);
                if ($diffHour >= 6) {
                    $this->restartUser($user);
                }
            }
            return true;
        }

        throw new Exception("Not contains users with scope");
    }

    public function restartUser(UserEntity $user)
    {

        // Se ele tiver escopo é pq parou no meio do processo
        // Se não tiver, ele apenas se autenticou e não processeguiu
        // Ambos são deslogados, porém quando há escopo, o usuário recebe uma mensagem
        if (!empty($user->getScope())) {
            sendMessageWhatsapp(
                $user->getPhone(),
                "notContains",
                [
                    "🙍🏻‍♂️ Parece que não temos notícias suas há algum tempo! Para garantir a segurança da sua conta, precisaremos reiniciar o seu processo de login. Quando estiver pronto, é só fazer login novamente para continuar de onde parou, ok? até mais!"
                ],
                0,
                false
            );
        }

        $this->userRepository->restartUser($user);

        return true;
    }
}
