<?php

namespace App\UseCase;

use DateTime;
use Domain\Entities\PointEntity;
use Domain\Entities\UserEntity;
use Domain\Enums\EventsWahaEnum;
use Domain\Repositories\PointRepositoryInterface;
use Domain\Repositories\UserRepositoryInterface;
use Domain\UseCase\OptionUseCaseInterface;
use Exception;
use Gemini;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class HitPointUseCase implements OptionUseCaseInterface
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
        if (count($points) >= 6) {
            sendMessageWhatsapp(
                $number,
                $messageId,
                [
                    "🚫 Limite de confirmações excedido. Procure o administrador do sistema para mais informações."
                ],
                0
            );
            $this->returnToMenu($user, $number, $messageId);
            return false;
        }

        try {

            $point = new PointEntity(
                Uuid::uuid4()->toString(),
                "",
                false,
                new DateTime(),
                new DateTime()
            );
            $point->setUser($user);

            $point = $this->pointRepository->hitPoint($point);
            $points[] = $point->presentation();

            Log::info('Ponto batido com sucesso', [
                'uuid' => $point->getUuid(),
                'user_uuid' => $point->getUser()->getUuid(),
                'observation' => $point->getObservation(),
                'checked' => $point->getChecked()
            ]);

            $points = $this->getHitsToDay($user);
            $text = "";
            foreach ($points as $i => $point) {
                $index = array_key_exists($i, $this->indices) ? $this->indices[$i] : $this->indices[4];
                $obs = empty($point['observation']) ? ' sem observação' : $point['observation'];
                $confirmed = $point['checked'] == 'true' ? "✅" : "❌";
                $text = $text . "📌 " . $index . " " . $point['date'] . PHP_EOL . "⤷ " . $obs . PHP_EOL . "⤷ Confirmado: " . $confirmed . PHP_EOL;
            }

            sendMessageWhatsapp($number, $messageId, ["✅ ponto criado com sucesso."], 0);
            sendMessageWhatsapp($number, $messageId, [$text], 0);
            sendMessageWhatsapp($number, $messageId, [EventsWahaEnum::HITPOINTMENU], 1);
            return true;
        } catch (Exception $e) {
            Log::info("Erro na receive da HitPointUseCase.", ['message' => $e->getMessage()]);
            sendMessageWhatsapp(
                $number,
                $messageId,
                [EventsWahaEnum::SERVERERROR],
                1
            );
            return false;
        }
    }

    public function addObservation(UserEntity $user, string $number, ?string $messageId = null, string $message = "")
    {
        Log::info('Add observation to point...', [
            'uuid' => $user->getUuid(),
            'number' => $number,
            'messageId' => $messageId,
            'message' => $message
        ]);

        try {

            if (empty($message)) {
                throw new Exception("Empty observation");
            }
            $validate = $this->geminiValidMessage($message);
            if($validate){
                sendMessageWhatsapp($number, $messageId, ["❌ Não aceitamos esse tipo de mensagem, usuário poderá ser bloqueado se insistir em continuar ❌"], 0, true);
                $this->returnToMenu($user, $number, $messageId, $message);
                return true;
            }

            $point = $this->pointRepository->addObservationToLastPoint($user, $message);

            $points = $this->getHitsToDay($user);
            $text = "";
            foreach ($points as $i => $point) {
                $index = array_key_exists($i, $this->indices) ? $this->indices[$i] : $this->indices[4];
                $obs = empty($point['observation']) ? ' sem observação' : $point['observation'];
                $confirmed = $point['checked'] == 'true' ? "✅" : "❌";
                $text = $text . "📌 " . $index . " " . $point['date'] . PHP_EOL . "⤷ " . $obs . PHP_EOL . "⤷ Confirmado: " . $confirmed . PHP_EOL;
            }

            sendMessageWhatsapp($number, $messageId, ["✅ Observação criada com sucesso."], 0);
            sendMessageWhatsapp($number, $messageId, [$text], 1);

            Log::info('Email enviado com sucesso', [
                'uuid' => $user->getUuid(),
                'email' => $user->getEmail(),
                'number' => $number,
                'messageId' => $messageId
            ]);

            $this->returnToMenu($user, $number, $messageId);
            return true;
        } catch (Exception $e) {
            Log::info("Erro ao colocar observação no ponto.", [
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

    public function deletePoint(UserEntity $user, string $number, ?string $messageId = null, string $message = "")
    {
        Log::info('Delete point...', [
            'uuid' => $user->getUuid(),
            'number' => $number,
            'messageId' => $messageId
        ]);

        try {

            $point = $this->pointRepository->deleteLastPoint($user);

            $points = $this->getHitsToDay($user);
            $text = "";
            foreach ($points as $i => $point) {
                $index = array_key_exists($i, $this->indices) ? $this->indices[$i] : $this->indices[4];
                $obs = empty($point['observation']) ? ' sem observação' : $point['observation'];
                $confirmed = $point['checked'] == 'true' ? "✅" : "❌";
                $text = $text . "📌 " . $index . " " . $point['date'] . PHP_EOL . "⤷ " . $obs . PHP_EOL . "⤷ Confirmado: " . $confirmed . PHP_EOL;
            }

            sendMessageWhatsapp($number, $messageId, ["✅ ponto deletado com sucesso."], 0);
            sendMessageWhatsapp($number, $messageId, [$text], 1);

            Log::info('Email enviado com sucesso', [
                'uuid' => $user->getUuid(),
                'email' => $user->getEmail(),
                'number' => $number,
                'messageId' => $messageId
            ]);

            $this->returnToMenu($user, $number, $messageId);
            return true;
        } catch (Exception $e) {
            Log::info("Erro ao deletar ponto.", [
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

    public function geminiValidMessage(string $message)
    {
        try {

            $gemini = Gemini::client(env('GEMINIKEY'));

            $response = $gemini->geminiFlash()->generateContent(
                "Me diga sim se essa mensagem for maliciosa ou dizer palavras de baixo calão, ou não se não for, sem quebra de linhas. mensagem: " . $message
            );

            $message = $response->text();
            $message = str_replace('\n', '', $message);

            if($message == "Sim\n"){
                return true;
            }

            return false;
        } catch (Exception $e) {
            log::error("Gemini error", [
                'message' => $e->getMessage()
            ]);
            return false;
        }
    }
}
