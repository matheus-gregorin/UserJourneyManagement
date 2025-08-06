<?php

namespace App\UseCase;

use DateTime;
use Domain\Entities\UserEntity;
use Domain\Enums\EventsWahaEnum;
use Domain\Repositories\PointRepositoryInterface;
use Domain\Repositories\UserRepositoryInterface;
use Domain\UseCase\OptionUseCaseInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class checkThePointsOfTheMounthUseCase implements OptionUseCaseInterface
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
        try {
            $points = $this->getHitsToMounth($user);
            sendPdfHitsOfTheMounthEmail($user, $points, 0);

            if (empty($points)) {
                sendMessageWhatsapp(
                    $number,
                    $messageId,
                    [
                        "❌ Não é possivel fazer essa ação, sem pontos marcados neste mês."
                    ],
                    0
                );
                $this->returnToMenu($user, $number, $messageId);
                return true;
            }

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

            sendMessageWhatsapp($number, $messageId, [EventsWahaEnum::HITSTOMOUNTHMENU], 1);

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

    public function confirmHitsOfTheMounth(UserEntity $user, string $number, ?string $messageId = null, ?string $message = "")
    {
        try {
            $points = $this->getHitsToMounth($user);
            if (empty($points)) {
                sendMessageWhatsapp(
                    $number,
                    $messageId,
                    [
                        "❌ Não é possivel fazer essa ação, sem pontos marcados neste mês."
                    ],
                    0
                );
                $this->returnToMenu($user, $number, $messageId);
                return true;
            }

            $pointsEntities = [];
            foreach ($points as $point) {
                $pointEntity = $this->pointRepository->confirmHits($point['uuid']);
                $pointsEntities[] = $pointEntity->presentation();
            }

            sendMessageWhatsapp(
                $number,
                $messageId,
                [
                    "✅ Pontos confirmado com sucesso."
                ],
                0
            );

            sendMessageWhatsapp(
                $number,
                $messageId,
                [
                    "✉️ Enviamos o email com o pdf ao seu email: " . $user->getEmail()
                ],
                0
            );

            sendPdfHitsOfTheMounthEmail($user, $pointsEntities, 0);

            Log::info('Pontos confirmado com sucesso', [
                'uuid' => $user->getUuid(),
                'email' => $user->getEmail(),
                'number' => $number,
                'messageId' => $messageId
            ]);

            $this->returnToMenu($user, $number, $messageId);
            return true;
        } catch (Exception $e) {
            Log::info("Erro ao confirmar pontos batidos hoje.", [
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
        //TODO: Implement this method to retrieve hits for the current day
        return [];
    }

    public function getHitsToMounth(UserEntity $user)
    {
        try {
            $now = new DateTime();
            $inicioDoMes = (clone $now)->modify('first day of this month')->setTime(0, 0, 0);
            $fimDoMes    = (clone $now)->modify('last day of this month')->setTime(23, 59, 59);

            return $this->pointRepository->getByUserUuidWithDates(
                $user->getUuid(),
                $inicioDoMes->format('Y-m-d H:i:s'),
                $fimDoMes->format('Y-m-d H:i:s')
            );
        } catch (Exception $e) {
            Log::info('Erro ao buscar pontos batidos no mês: ', [
                'uuid' => $user->getUuid(),
                'message' => $e->getMessage()
            ]);
            return [];
        }
    }
}
