<?php

namespace App\UseCase;

use App\Domain\Entities\UserEntity;
use App\Domain\Enums\EventsWahaEnum;
use App\Domain\Enums\ValidationIAEnum;
use App\Domain\HttpClients\ClientHttpInterface;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Exceptions\CollectUserByPhoneException;
use App\Exceptions\UpdateScopeException;
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
    private array $scopes = [
        "1" => 'checkThePointsHitToday',
        "2" => 'CheckIn',
        "3" => 'clockOutForLunch',
        "4" => 'clockBackFromLunch',
        "5" => 'CheckOut',
        "6" => 'Support'
    ];

    private array $options = [
        'checkThePointsHitToday' => [
            "1" => 'sendEmailPdf',
            "2" => 'returnToMenu'
        ]
    ];

    public function __construct(
        UserRepositoryInterface $userRepository,
        ClientHttpInterface $clientHttp
    ) {
        $this->IA = Gemini::client(env('GEMINIKEY'));
        $this->userRepository = $userRepository;
        $this->clientHttp = $clientHttp;
    }

    public function webhookReceiveMessage(array $payload)
    {

        Log::info('MESSAGE RECEIVE', ['payload' => $payload]);
        $send = sendMessage(
            "5511956558187@c.us",
            "false_5511951651712@c.us_3F21C4FC29E36870B975",
            "TESTE",
            0
        );
        dd("Fim", $send);

        $event = $payload['event'];
        if ($event == EventsWahaEnum::MESSAGE) {
            try {

                // Valid Payload
                $handledPayload = $this->validPayload($payload);
                if (!$handledPayload) {
                    Log::info('PAYLOAD NOT VALID', ['payload' => $payload]);
                    return true;
                }

                // Quebra a string a partir do @ e coleta o primeiro elemento, que é o número do telefone
                $numberSearch = explode('@', $handledPayload['number'])[0];
                $user = $this->userRepository->getUserWithPhoneNumber($numberSearch);

                // Verifica se o usuário já se autenticou
                if ($user->getIsAuth()) {

                    // Se enviar uma opção valida
                    if (strtoupper($handledPayload['message']) == "MENU") {
                        Log::info('USER REQUEST MENU', [
                            'username' => $user->getName(),
                            'is_auth' => $user->getIsAuth()
                        ]);
                        $this->userRepository->updateScopeOfTheUser($user, "");
                        $this->sendMessage($handledPayload['number'], $handledPayload['messageId'], EventsWahaEnum::SCOPE, 2);
                        return true;
                    } elseif (!empty($user->getScope())) {

                        Log::info("USER CONTAIN SCOPE", [
                            'username' => $user->getName(),
                            'number' => $handledPayload['number'],
                            'scope' => $user->getScope()
                        ]);

                        $scopeCurrent = $user->getScope();
                        $option = "";
                        if (array_key_exists($handledPayload['message'], $this->options[$scopeCurrent])) {
                            $option = $this->options[$scopeCurrent][$handledPayload['message']];
                        } else {
                            Log::info('USER SCOPE NOT CONTAIN OPTION', [
                                'username' => $user->getName(),
                                'is_auth' => $user->getIsAuth(),
                                'scope' => $scopeCurrent,
                                'option' => $handledPayload['message']
                            ]);
                            throw new Exception('MESSAGE NOT UNDERSTOOD');
                        }

                        $this->dispatchOption($user, $scopeCurrent, $option, $handledPayload['number'], $handledPayload['messageId']);
                        return true;

                    } elseif (empty($user->getScope()) && array_key_exists($handledPayload['message'], $this->scopes)) {

                        $option = $handledPayload['message'];
                        $scope = $this->scopes[$option];

                        Log::info('USER NOT CONTAIN SCOPE, INIT', [
                            'username' => $user->getName(),
                            'is_auth' => $user->getIsAuth(),
                            'option' => $option,
                            'scope' => $scope
                        ]);
                        $this->userRepository->updateScopeOfTheUser($user, $scope);
                        $this->dispatchOption($user, $scope, "", $handledPayload['number'], $handledPayload['messageId']);
                        return true;
                    } else {
                        Log::info('MESSAGE NOT UNDERSTOOD', [
                            'message' => $handledPayload['message'],
                            'user' => json_encode($user->toArray())
                        ]);
                        throw new Exception('MESSAGE NOT UNDERSTOOD');
                    }
                    return true;
                } else {

                    // Faz a autenticação
                    if (str_contains($handledPayload['message'], 'OTPU')) {
                        $auth = $this->AuthUserByCodeOtp($user, $handledPayload['message'], $handledPayload['number'], $handledPayload['messageId']);
                        if ($auth) {
                            return true;
                        }
                    }
                    if (!str_contains($handledPayload['message'], 'OTPU')) {
                        // Inicia o processo de autenticação enviando mensagem de boas vindas mais código OTP
                        $this->sendMessage($handledPayload['number'], $handledPayload['messageId'], EventsWahaEnum::HI . $user->getName() . EventsWahaEnum::USERNOTAUTH, 2);
                    }

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
                    'number' => $handledPayload['number'],
                    'message' => $e->getMessage()
                ]);
                $this->clientHttp->sendError($handledPayload['number'], EventsWahaEnum::USERNOTFOUND);
                return false;
            } catch (CollectUserByPhoneException $e) {
                Log::critical('COLLECT USER BY PHONE EXCEPTION', [
                    'number' => $handledPayload['number'],
                    'message' => $e->getMessage()
                ]);
                $this->clientHttp->sendError($handledPayload['number'], EventsWahaEnum::MESSAGENOTUNDERSTOOD);
                return false;
            } catch (UpdateScopeException $e) {
                Log::critical('UPDATED SCOPE EXCEPTION', [
                    'number' => $handledPayload['number'],
                    'message' => $e->getMessage()
                ]);
                $this->clientHttp->sendError($handledPayload['number'], EventsWahaEnum::MESSAGENOTUNDERSTOOD);
                return false;
            } catch (Exception $e) {
                Log::critical('PROCESS ERROR', [
                    'number' => $handledPayload['number'] ?? "Not number",
                    'message' => $e->getMessage()
                ]);
                $this->clientHttp->sendError($handledPayload['number'], EventsWahaEnum::MESSAGENOTUNDERSTOOD);
                return false;
            }
        }

        Log::info('EVENT WHATSAPP RECEIVE ERROR', [
            'event' => $event,
            'payload' => $payload
        ]);
        return false;
    }

    public function dispatchOption(UserEntity $user, string $scope, string $option = "", string $number, string $messageId,)
    {
        Log::info(
            "OPTION SELECTED: ",
            [
                "scope" => $scope,
                "option" => $option,
            ]
        );
        $scopeUseCase = OptionsFactory::getOptions($scope);

        if (!empty($option)) {
            $scopeUseCase->$option($user, $number, $messageId);
            return true;
        }

        $scopeUseCase->receive($user, $number, $messageId);
        return true;
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
                $this->sendMessage($number, $messageId, EventsWahaEnum::MENU, 1);
                $this->sendMessage($number, $messageId, EventsWahaEnum::SCOPE, 2);

                Log::info('USER REQUEST AUTH SUCCESS', [
                    'username' => $user->getName(),
                    'is_auth' => $user->getIsAuth()
                ]);
                return true;
            }
            Log::critical('CODE OTPU INVALID', [
                'username' => $user->getName(),
                'number' => $number,
                'user_code' => $user->getOtpCode(),
                'message_code' => $message
            ]);
            $this->clientHttp->sendError($number, EventsWahaEnum::CODEINVALIDRESEND);
            return false;
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

    public function validPayload(array $payload)
    {
        try {
            $content = [];
            $number = !empty($payload['payload']['from']) ? $payload['payload']['from'] : false;
            if (!$number) {
                Log::info('NUMBER NOT FOUND', ['payload' => $payload]);
                return false;
            }
            $content['number'] = $number;
            Log::info('NUMBER', ['number' => $content]);

            $messageId = !empty($payload['payload']['id']) ? $payload['payload']['id'] : false;
            if (!$messageId) {
                throw new Exception('Message ID not found');
            }
            $content['messageId'] = $messageId;
            Log::info('MESSAGE ID', ['messageId' => $messageId]);

            // Verifica se o payload contém o corpo da mensagem
            if (empty($payload['payload']['body']) || !is_string($payload['payload']['body']) || $payload['payload']['body'] === "0") {
                Log::info('MESSAGE NOT FOUND', ['payload' => $payload]);
                throw new Exception('Message not found');
            }
            $content['message'] = $payload['payload']['body'];
            Log::info('MESSAGE RECEIVE', ['messageReceive' => $payload['payload']['body']]);

            return $content;
        } catch (Exception $e) {
            $this->clientHttp->sendError($payload['payload']['from'], EventsWahaEnum::MESSAGENOTUNDERSTOOD);
            return false;
        }
    }
}
