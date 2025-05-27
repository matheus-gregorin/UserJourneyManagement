<?php

namespace App\UseCase;

use App\Domain\Entities\UserEntity;
use App\Domain\Repositories\CheckThePointsHitTodayUseCaseInterface;
use App\Domain\Repositories\OptionUseCaseInterface;
use App\Domain\Repositories\PointRepositoryInterface;
use App\Jobs\ResponseMessageJob;
use DateTime;
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
            $now = new DateTime();
            $points = $this->pointRepository->getByUserUuidWithDates($user->getUuid(), (clone $now)->setTime(0, 0, 0)->format('Y-m-d H:i:s'), (clone $now)->setTime(23, 59, 0)->format('Y-m-d H:i:s'));
            $this->sendMessage($number, $messageId, 'Colaborador: ' . $user->getName() .".", 0);
            $this->sendMessage($number, $messageId, 'Pontos do dia:', 0);
            $text = "";
            $indices = [
                0 => "_*Entrada:*_",
                1 => "_*Almoço (Início):*_",
                2 => "_*Almoço (Fim):*_",
                3 => "_*Saída:*_",
                4 => "_*Observação:*_"
            ];
            foreach ($points as $i => $point) {
                $index = array_key_exists($i, $indices) ? $indices[$i] : $indices[4];
                $text = $text . $index . " " . $point['date'] . PHP_EOL;
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
