<?php

namespace App\UseCase;

use App\Domain\Entities\UserEntity;
use App\Domain\Enums\EventsWahaEnum;
use App\Domain\Enums\ValidationIAEnum;
use App\Domain\HttpClients\ClientHttpInterface;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Exceptions\CollectUserByPhoneException;
use App\Exceptions\UserNotFoundException;
use App\Factorys\OptionsFactory;
use App\Jobs\ResponseMessageJob;
use App\Jobs\sendCodeEmailJob;
use Exception;
use Gemini;
use Gemini\Client;
use Illuminate\Support\Facades\Log;

class WebhookReceiveMessageWahaUseCase
{

    private Client $IA;
    private UserRepositoryInterface $userRepository;
    private ClientHttpInterface $clientHttp;
    private array $themes = [
        "1" => 'checkThePointsHitToday',
        "2" => 'CheckIn',
        "3" => 'clockOutForLunch',
        "4" => 'clockBackFromLunch',
        "5" => 'CheckOut',
        "6" => 'Support'
    ];

    public function __construct(
        UserRepositoryInterface $userRepository,
        ClientHttpInterface $clientHttp
    ) {
        $this->IA = Gemini::client(env('GEMINIKEY'));
        $this->IA->geminiFlash()->generateContent(
            ValidationIAEnum::SETUP
        );
        $this->userRepository = $userRepository;
        $this->clientHttp = $clientHttp;
    }

    public function webhookReceiveMessage(array $payload)
    {

        Log::info('MESSAGE RECEIVE', ['payload' => $payload]);

        $event = $payload['event'];
        if ($event == EventsWahaEnum::MESSAGE) {
            try {

                $number = !empty($payload['payload']['from']) ? $payload['payload']['from'] : false;
                if (!$number) {
                    Log::info('NUMBER NOT FOUND', ['payload' => $payload]);
                    return false;
                }
                Log::info('NUMBER', ['number' => $number]);

                $messageId = !empty($payload['payload']['id']) ? $payload['payload']['id'] : false;
                if (!$messageId) {
                    throw new Exception('Message ID not found');
                }
                Log::info('MESSAGE ID', ['messageId' => $messageId]);

                $message = !empty($payload['payload']['body']) ? $payload['payload']['body'] : false;
                if (!$message) {
                    throw new Exception('Message not found');
                }
                Log::info('MESSAGE RECEIVE', ['messageReceive' => $message]);

                // Quebra a string a partir do @ e coleta o primeiro elemento, que é o número do telefone
                $numberSearch = explode('@', $number)[0];
                $user = $this->userRepository->getUserWithPhoneNumber($numberSearch);

                // Verifica se o usuário já se autenticou
                if ($user->getIsAuth()) {
                    // Validação da mensagem
                    //$validation = $this->maliciousMessageValidation($number, $message);
                    if (true) {
                        // Se enviar uma opção valida
                        if (array_key_exists($message, $this->themes)) {
                            $option = $message;
                            $choice = $this->themes[$option];
                            $this->dispatchOption($user, $choice);
                        } else {
                            $this->sendMessage($number, $messageId, EventsWahaEnum::MESSAGERESEND, 2);
                            $this->sendMessage($number, $messageId, EventsWahaEnum::SCOPE, 2);
                        }

                        return true;
                    } else {
                        throw new Exception('MESSAGE NOT UNDERSTOOD');
                        Log::info('MESSAGE NOT UNDERSTOOD', [
                            'message' => $message,
                            'user' => json_encode($user->toArray())
                        ]);
                    }
                } else {

                    // Faz a autenticação
                    if (str_contains($message, 'OTPU')) {
                        $this->AuthUserByCodeOtp($user, $message, $number, $messageId);
                        return true;
                    }

                    // Inicia o processo de autenticação enviando mensagem de boas vindas mais código OTP
                    $this->sendMessage($number, $messageId, EventsWahaEnum::HI . $user->getName() . EventsWahaEnum::USERNOTAUTH, 2);

                    // Criar codigo de autenticação
                    $otp = "OTPU" . rand(100000, 999999);
                    $user->setOtpCode($otp);
                    $this->userRepository->updateOTP($user);
                    Log::info('USER OTP UPDATE SUCCESS', [
                        'user' => json_encode($user->toArray()),
                        'otp' => $otp
                    ]);

                    // Envia código para o email
                    $this->sendEmail($user, $otp, 2);

                    return true;
                }
            } catch (UserNotFoundException $e) {
                Log::critical('USER NOT FOUND EXCEPTION', [
                    'number' => $number,
                    'message' => $e->getMessage()
                ]);
                $this->clientHttp->sendError($number, EventsWahaEnum::USERNOTFOUND);
                return false;
            } catch (CollectUserByPhoneException $e) {
                Log::critical('COLLECT USER BY PHONE EXCEPTION', [
                    'number' => $number,
                    'message' => $e->getMessage()
                ]);
                $this->clientHttp->sendError($number, EventsWahaEnum::MESSAGENOTUNDERSTOOD);
                return false;
            } catch (Exception $e) {
                Log::critical('PROCESS ERROR', [
                    'number' => $number ?? "Not number",
                    'message' => $e->getMessage()
                ]);
                $this->clientHttp->sendError($number, EventsWahaEnum::MESSAGENOTUNDERSTOOD);
                return false;
            }
        }

        Log::info('EVENT WHATSAPP RECEIVE ERROR', [
            'event' => $event,
            'payload' => $payload
        ]);
        return false;
    }

    public function dispatchOption(UserEntity $user, string $choice)
    {
        $OptionUseCase = OptionsFactory::getOptions($choice);
        $OptionUseCase->receive($user);
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

    public function sendEmail(UserEntity $user, string $otp, int $delay = 0)
    {
        try {
            // Envia o email aqui
            sendCodeEmailJob::dispatch(
                $user->getEmail(),
                $user->getName(),
                $otp
            )->delay(now()->addSeconds($delay));
            return true;
        } catch (Exception $e) {
            Log::info('SEND EMAIL ERROR', [
                'username' => $user->getName(),
                'email' => $user->getEmail(),
                'otp' => $otp,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function maliciousMessageValidation(string $number, string $message)
    {
        try {
            $validation = $this->IA->geminiFlash()->generateContent(
                ValidationIAEnum::VALIDCONTENT .
                    ValidationIAEnum::RETURNVALIDCONTENT .
                    $message
            );

            if (!$validation) {
                Log::info('VALIDATION MESSAGE FAILED', [
                    'number' => $number,
                    'message_validation' => $validation,
                    'message' => $message
                ]);
                return false;
            }
            $validation = json_decode($validation->text(), true);
            Log::info('VALIDATION SUCCESS', ['message' => $validation]);

            if (!empty($validation['its_okay']) && $validation['its_okay']) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            Log::critical('VALIDATION MESSAGE ERROR', [
                'number' => $number,
                'message_validation' => $validation,
                'message' => $message,
                'message' => $e->getMessage()
            ]);
            $this->clientHttp->sendError($number, EventsWahaEnum::MESSAGENOTUNDERSTOOD);
            return false;
        }
    }

    public function AuthUserByCodeOtp(UserEntity $user, string $message, string $number, string $messageId)
    {
        try {

            // Valida e autentica o usuário, depois envia as opções
            Log::info('USER REQUEST AUTH', [
                'username' => $user->getName(),
                'is_auth' => $user->getIsAuth(),
                'valid' => $user->getOtpCode() == $message,
                'otp_db' => $user->getOtpCode(),
                'otp_message' => $message
            ]);

            if ($user->getOtpCode() == $message) {
                $user->setIsAuth(true);
                $this->userRepository->authUser($user);

                $this->sendMessage($number, $messageId, $user->getName() . EventsWahaEnum::AWAIT, 0);
                $this->sendMessage($number, $messageId, EventsWahaEnum::AUTHSUCCESS, 1);
                $this->sendMessage($number, $messageId, EventsWahaEnum::SCOPE, 2);

                Log::info('USER REQUEST AUTH SUCCESS', [
                    'username' => $user->getName(),
                    'is_auth' => $user->getIsAuth()
                ]);
            }
            return true;
        } catch (Exception $e) {
            Log::critical('USER REQUEST AUTH FAILED', [
                'username' => $user->getName(),
                'number' => $number,
                'message' => $e->getMessage()
            ]);
            $this->clientHttp->sendError($number, EventsWahaEnum::MESSAGENOTUNDERSTOOD);
            return false;
        }
    }
}
