<?php

namespace App\UseCase;

use Domain\Entities\UserEntity;
use Domain\Enums\EventsWahaEnum;
use Domain\UseCase\OptionUseCaseInterface;
use Domain\Repositories\PointRepositoryInterface;
use Domain\Repositories\UserRepositoryInterface;
use App\Jobs\SendWhatsappMessageJob;
use App\Jobs\SendHitsEmailJob;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;

class CheckThePointsHitTodayUseCase implements OptionUseCaseInterface
{
    private UserRepositoryInterface $userRepository;
    private PointRepositoryInterface $pointRepository;

    private array $indices = [
        0 => "_*Entrada:*_",
        1 => "_*Almoço (Início):*_",
        2 => "_*Almoço (Fim):*_",
        3 => "_*Saída:*_",
        4 => "_*Observação:*_"
    ];

    public function __construct(
        PointRepositoryInterface $pointRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->pointRepository = $pointRepository;
        $this->userRepository = $userRepository;
    }

    public function receive(UserEntity $user, string $number, ?string $messageId = null)
    {
        $points = $this->getHitsToDay($user);
        if (empty($points)) {
            $this->sendMessage($number, $messageId, "Sem pontos batidos no dia de hoje.", 1);
            $this->sendMessage($number, $messageId, EventsWahaEnum::HITSTODAYMENU, 1);
            return true;
        }

        try {
            $this->sendMessage($number, $messageId, 'Colaborador: ' . $user->getName() . ".", 0);
            $this->sendMessage($number, $messageId, 'Pontos do dia:', 0);

            $text = "";
            foreach ($points as $i => $point) {
                $index = array_key_exists($i, $this->indices) ? $this->indices[$i] : $this->indices[4];
                $text = $text . $index . " " . $point['date'] . PHP_EOL;
            }
            $this->sendMessage($number, $messageId, $text, 1);
            $this->sendMessage($number, $messageId, EventsWahaEnum::HITSTODAYMENU, 1);
            return true;
        } catch (Exception $e) {
            Log::info("Erro na receive da CheckThePointsHitTodayUseCase.", ['message' => $e->getMessage()]);
            $this->sendMessage($number, $messageId, EventsWahaEnum::SERVERERROR, 1);
            return false;
        }
    }

    public function getHitsToDay(UserEntity $user)
    {
        try {
            $now = new DateTime();
            return $this->pointRepository->getByUserUuidWithDates($user->getUuid(), (clone $now)->setTime(0, 0, 0)->format('Y-m-d H:i:s'), (clone $now)->setTime(23, 59, 0)->format('Y-m-d H:i:s'));
        } catch (Exception $e) {
            Log::info('Erro ao buscar pontos batidos hoje: ', [
                'uuid' => $user->getUuid(),
                'message' => $e->getMessage()
            ]);
            return [];
        }
    }

    public function sendEmailPdf(UserEntity $user, string $number, ?string $messageId = null)
    {
        $points = $this->getHitsToDay($user);
        $this->sendEmail($user, $points, 0);
        $this->sendMessage($number, $messageId, "Enviamos o email com o pdf ao seu email: " . $user->getEmail() , 0);
                $this->sendMessage($number, $messageId, "Retornando ao menu..." , 0);
        $this->returnToMenu($user, $number, $messageId);
        Log::info('Email enviado com sucesso', [
            'uuid' => $user->getUuid(),
            'email' => $user->getEmail(),
            'number' => $number,
            'messageId' => $messageId
        ]);
        return true;
    }

    public function returnToMenu(UserEntity $user, string $number, ?string $messageId = null)
    {
        Log::info('Returning to menu for user', [
            'uuid' => $user->getUuid(),
            'number' => $number,
            'messageId' => $messageId
        ]);
        $this->userRepository->updateScopeOfTheUser($user, "");
        $this->sendMessage($number, $messageId, EventsWahaEnum::SCOPE, 1);
        return true;
    }

    public function sendEmail(UserEntity $user, array $hits, int $delay = 0)
    {
        try {
            // Envia o email aqui
            SendHitsEmailJob::dispatch(
                $user->getEmail(),
                $user->getName(),
                $hits
            )->delay(now()->addSeconds($delay));
            return true;
        } catch (Exception $e) {
            Log::info('SEND EMAIL ERROR', [
                'username' => $user->getName(),
                'email' => $user->getEmail(),
                'otp' => $hits,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendMessage(string $number, string $messageId, string $message, int $delay = 0)
    {
        try {
            // Envia mensagem aqui
            SendWhatsappMessageJob::dispatch(
                $number,
                $messageId,
                $message
            )->delay(now()->addSeconds($delay));
            return true;
        } catch (Exception $e) {
            Log::info('SEND MESSAGE ERROR', [
                'number' => $number,
                'messageId' => $messageId,
                'message' => $message,
                'error' => $e->getMessage()
            ]);
        }
    }
}
