<?php

namespace App\UseCase;

use Domain\Entities\UserEntity;
use Domain\Enums\EventsWahaEnum;
use Domain\UseCase\OptionUseCaseInterface;
use Domain\Repositories\PointRepositoryInterface;
use Domain\Repositories\UserRepositoryInterface;
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
            sendMessageWhatsapp(
                $number,
                $messageId,
                [
                    "Sem pontos batidos no dia de hoje."
                ],
                0
            );
            $this->returnToMenu($user, $number, $messageId);
            return true;
        }

        try {
            sendMessageWhatsapp($number, $messageId, ['Colaborador: ' . $user->getName()], 0);
            // Enviar nome da empresa
            //sendMessageWhatsapp($number, $messageId, ['Colaborador: ' . $user->getName()], 0);

            $text = "";
            foreach ($points as $i => $point) {
                $index = array_key_exists($i, $this->indices) ? $this->indices[$i] : $this->indices[4];
                $obs = empty($point['observation']) ? ' sem observação' : $point['observation'];
                $confirmed = $point['checked'] == 'true' ? "✅" : "❌";
                $text = $text . "📌 " . $index . " " . $point['date'] . PHP_EOL . "⤷ Obs: " . $obs . PHP_EOL . "⤷ Confirmado: " . $confirmed . PHP_EOL;
            }

            sendMessageWhatsapp($number, $messageId, [$text], 0);
            sendMessageWhatsapp($number, $messageId, [EventsWahaEnum::HITSTODAYMENU], 1);

            return true;
        } catch (Exception $e) {
            Log::info("Erro na receive da CheckThePointsHitTodayUseCase.", ['message' => $e->getMessage()]);
            sendMessageWhatsapp($number, $messageId, [EventsWahaEnum::SERVERERROR], 1);
            return false;
        }
    }

    public function sendEmailPdf(UserEntity $user, string $number, ?string $messageId = null, ?string $message = "")
    {
        try {
            $points = $this->getHitsToDay($user);
            sendPdfHitsTodayEmail($user, $points, 0);
            sendMessageWhatsapp(
                $number,
                $messageId,
                [
                    "✉️ Enviamos o email com o pdf ao seu email: " . $user->getEmail()
                ],
                0
            );

            Log::info('Email enviado com sucesso', [
                'uuid' => $user->getUuid(),
                'email' => $user->getEmail(),
                'number' => $number,
                'messageId' => $messageId
            ]);

            $this->returnToMenu($user, $number, $messageId);
            return true;
        } catch (Exception $e) {
            Log::info("Erro ao enviar email com PDF dos pontos batidos hoje.", [
                'uuid' => $user->getUuid(),
                'message' => $e->getMessage()
            ]);
            sendMessageWhatsapp(
                $number,
                $messageId,
                [EventsWahaEnum::SERVERERROR],
                1
            );
            return false;
        }
    }

    public function returnToMenu(UserEntity $user, string $number, ?string $messageId = null, ?string $message = "")
    {
        Log::info('Returning to menu for user', [
            'uuid' => $user->getUuid(),
            'number' => $number,
            'messageId' => $messageId
        ]);
        $this->userRepository->updateScopeOfTheUser($user, "");

        sendMessageWhatsapp($number, $messageId, ["🔙 Retornando ao menu principal"], 1);
        sendMessageWhatsapp($number, $messageId, [EventsWahaEnum::SCOPE], 3);
        return true;
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
}
