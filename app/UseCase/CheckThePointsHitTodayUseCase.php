<?php

namespace App\UseCase;

use App\Domain\Entities\UserEntity;
use App\Domain\Repositories\CheckThePointsHitTodayUseCaseInterface;
use App\Domain\Repositories\OptionUseCaseInterface;
use App\Domain\Repositories\PointRepositoryInterface;
use App\Jobs\ResponseMessageJob;
use Exception;
use Illuminate\Support\Facades\Log;

class CheckThePointsHitTodayUseCase implements OptionUseCaseInterface
{
    private PointRepositoryInterface $pointRepository;

    public function __construct(PointRepositoryInterface $pointRepository)
    {
        $this->pointRepository = $pointRepository;
    }

    public function receive(UserEntity $user, string $number, ?string $messageId = null)
    {
        try {
            $points = $this->pointRepository->getByUserUuidWithDates($user->getUuid(), date('2025-05-23 00:00:00'), date('2025-05-23 23:59:59'));
            $this->sendMessage($number, $messageId, 'Colaborador: ' . $user->getName() . ', pontos de hoje:', 0);
            $text = "";
            foreach ($points as $point) {
                $text = $text . "Data/Hora: " . $point['date'] . PHP_EOL;
            }
            $this->sendMessage($number, $messageId, $text, 1);
            return true;
        } catch (Exception $e) {
            Log::info($e->getMessage());
            $this->sendMessage($number, $messageId, "Sem pontos batidos no dia de hoje.", 1);
        }
    }

    public function sendMessage(string $number, string $messageId, string $message, int $delay = 0)
    {
        try {
            // Envia mensagem aqui
            ResponseMessageJob::dispatch(
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
