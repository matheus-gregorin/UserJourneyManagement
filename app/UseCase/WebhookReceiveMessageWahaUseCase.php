<?php

namespace App\UseCase;

use App\Domain\Entities\UserEntity;
use App\Domain\Enums\EventsWahaEnum;
use App\Domain\Enums\ValidationIAEnum;
use App\Domain\HttpClients\ClientHttpInterface;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Exceptions\CollectUserByPhoneException;
use App\Exceptions\UserNotFoundException;
use App\Http\HttpClients\WahaHttpClient;
use App\Jobs\ResponseMessageJob;
use App\Jobs\sendCodeEmailJob;
use App\Mail\CodeMail;
use Exception;
use Gemini;
use Gemini\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WebhookReceiveMessageWahaUseCase
{

    private Client $IA;
    private UserRepositoryInterface $userRepository;
    private ClientHttpInterface $clientHttp;
    private array $themes = [
        '1' => '',
        '2' => '',
        '3' => '',
        '4' => '',
        '5' => '',
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

            // Add try catch para tratamento
            try {

                $number = !empty($payload['payload']['from']) ? $payload['payload']['from'] : false;
                if (!$number) {
                    throw new Exception('Number not found');
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

                // Quebra a string a partir do @ e coleta o primeiro elemento, que é o número do tolefone
                $numberSearch = explode('@', $number)[0];
                $user = $this->userRepository->getUserWithPhoneNumber($numberSearch);

                // Autenticação
                if (str_contains($message, 'OTPU') && !$user->getIsAuth()) {
                    $this->AuthUserByCodeOtp($user, $message, $number, $messageId);
                    return true;
                }

                // Verifica se o usuário já se autenticou
                if ($user->getIsAuth()) {

                    // Valida a mensagem //VERIFICAR SE A OPÇÂO ESCOLHIDA CONDIZ COM AS OPÇÔES ACIMA!!
                    $validation = $this->maliciousMessageValidation($message);
                    if ($validation && in_array($message, $this->themes)) {

                        // // Envia a mensagem via Job
                        // ResponseMessageJob::dispatch(
                        //     $number,
                        //     $messageId,
                        //     $response
                        // )->delay(now()->addSeconds(5));

                        return true;
                    } else {
                        throw new Exception('Message not understood');
                        Log::info('MESSAGE NOT UNDERSTOOD', [
                            'message' => $message,
                            'user' => json_encode($user->toArray())
                        ]);
                    }
                } else {

                    // Envia a mensagem via Job
                    ResponseMessageJob::dispatch(
                        $number,
                        $messageId,
                        "Olá, " . $user->getName() . ", tudo bem? Você ainda não está autenticado. Para isso, enviei um codigo no seu email: " . $user->getEmail() . " por favor, verifique se chegou e me envie o código para que eu possa te ajudar. Caso não tenha recebido, entre em contato com seu supervisor ou acesse o nosso suporte em www.userManager.com.br"
                    )->delay(now()->addSeconds(5));

                    // Criar codigo de autenticação
                    $otp = "OTPU" . rand(100000, 999999);
                    $user->setOtpCode($otp);
                    $this->userRepository->updateOTP($user);
                    Log::info('User OTP update success', [
                        'user' => json_encode($user->toArray()),
                        'otp' => $otp
                    ]);

                    // Envia o email aqui
                    sendCodeEmailJob::dispatch(
                        $user->getEmail(),
                        $user->getName(),
                        $otp
                    )->delay(now()->addSeconds(3));

                    return true;
                }
            } catch (UserNotFoundException $e) {
                Log::critical('USER NOT FOUND EXCEPTION', [
                    'number' => $number,
                    'message' => $e->getMessage()
                ]);
                //$this->clientHttp->sendError($number, EventsWahaEnum::USERNOTFOUND);
                return true;
            } catch (CollectUserByPhoneException $e) {
                Log::critical('COLLECT USER BY PHONE EXCEPTION', [
                    'number' => $number,
                    'message' => $e->getMessage()
                ]);
                //$this->clientHttp->sendError($number, EventsWahaEnum::MESSAGENOTUNDERSTOOD);
                return true;
            } catch (Exception $e) {
                Log::critical('PROCESS ERROR', [
                    'number' => $number ?? "Not number",
                    'message' => $e->getMessage()
                ]);
                //$this->clientHttp->sendError($number, EventsWahaEnum::MESSAGENOTUNDERSTOOD);
                return true;
            }
        }

        Log::info('Event whatsapp receive error', [
            'event' => $event,
            'payload' => $payload
        ]);

        return false;
    }

    public function maliciousMessageValidation(string $message)
    {
        $validation = $this->talkIA(
            ValidationIAEnum::VALIDATION,
            $message
        );
        if (!$validation) {
            Log::info('VALIDATION MESSAGE FAILED', [
                'message_validation' => $validation,
                'message' => $message
            ]);
            return false;
        }
        $validation = json_decode($validation->text(), true);

        Log::info('VALIDATION SUCCESS', ['message' => $validation]);

        if ($validation['its_okay']) {
            return true;
        }
        return false;
    }

    public function AuthUserByCodeOtp(UserEntity $user, string $message, string $number, string $messageId)
    {
        try {

            // Valida e autentica o usuário, depois envia as demais opções
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

                // Envia a mensagem via Job
                ResponseMessageJob::dispatch(
                    $number,
                    $messageId,
                    $user->getName() . EventsWahaEnum::AWAIT
                )->delay(now()->addSeconds(1));

                // Envia a mensagem via Job
                ResponseMessageJob::dispatch(
                    $number,
                    $messageId,
                    EventsWahaEnum::SCOPE
                )->delay(now()->addSeconds(1));

                Log::info('USER REQUEST AUTH SUCCESS', [
                    'username' => $user->getName(),
                    'is_auth' => $user->getIsAuth()
                ]);
            }
            return true;

        } catch (Exception $e){
            Log::critical('USER REQUEST AUTH FAILED', [
                'username' => $user->getName(),
                'number' => $number,
                'message' => $e->getMessage()
            ]);
            $this->clientHttp->sendError($number, EventsWahaEnum::MESSAGENOTUNDERSTOOD);
            return false;

        }
    }

    public function talkIA(string $case, string $message, ?UserEntity $user = null)
    {

        $Ia = $this->IA->geminiFlash();

        switch ($case) {
            case ValidationIAEnum::VALIDATION:
                return $Ia->generateContent(
                    ValidationIAEnum::VALIDCONTENT .
                    ValidationIAEnum::RETURNVALIDCONTENT . 
                    $message
                );
                break;
            default:
                Log::info('CASE NOT FOUND', ['case' => $case]);
                return false;
                break;
        }
    }
}
