<?php

namespace App\UseCase;

use Domain\Entities\UserEntity;
use Domain\Enums\EventsWahaEnum;
use Domain\Repositories\CompanyRepositoryInterface;
use Domain\Repositories\PointRepositoryInterface;
use Domain\Repositories\UserRepositoryInterface;
use Domain\UseCase\OptionUseCaseInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class CallSupportUseCase implements OptionUseCaseInterface
{

    private UserRepositoryInterface $userRepository;
    private PointRepositoryInterface $pointRepository;
    private CompanyRepositoryInterface $companyRepository;

    public function __construct(
        PointRepositoryInterface $pointRepository,
        UserRepositoryInterface $userRepository,
        CompanyRepositoryInterface $companyRepository
    ) {
        $this->pointRepository = $pointRepository;
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
    }

    public function receive(UserEntity $user, string $number, ?string $messageId = null)
    {
        try {

            $messageBySupport = "📞 *Usuário:* {$user->getName()}\n📱 *Número:* {$number}\n🏢 *Empresa:* {$user->getCompany()->getFantasyName()}\n\nSolicita atendimento do suporte.";

            // Message by suport
            sendMessageWhatsapp(
                '5511956558187@c.us',
                $messageId,
                [
                    $messageBySupport
                ],
                0
            );

            $messageAwait = "⏳ Aguarde um momento, estamos enviando sua solicitação de suporte.\n Em breve um atendente entrará em contato com você.";
            $messageToCalled = "Você pode cancelar a solicitação de suporte a qualquer momento digitando *1*.";

            // Message by user, to await the support
            sendMessageWhatsapp(
                $number,
                $messageId,
                [
                    $messageAwait,
                    $messageToCalled
                ],
                1
            );

            Log::info('Suporte enviado com sucesso', [
                'uuid' => $user->getUuid(),
                'email' => $user->getEmail(),
                'number' => $number,
                'messageId' => $messageId
            ]);

            return true;
        } catch (Exception $e) {
            Log::info("Erro ao enviar mensagem solicitando suporte.", [
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

    public function returnToMenu(UserEntity $user, string $number, ?string $messageId = null)
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
        //
    }
}
