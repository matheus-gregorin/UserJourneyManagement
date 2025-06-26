<?php

namespace App\UseCase;

use Domain\Entities\UserEntity;
use Domain\Enums\EventsWahaEnum;
use Domain\Repositories\UserRepositoryInterface;
use App\Exceptions\CollectUserByPhoneException;
use App\Exceptions\UpdateScopeException;
use App\Exceptions\UserNotFoundException;
use App\Factorys\OptionsFactory;
use Exception;
use Illuminate\Support\Facades\Log;

class WebhookReceiveMessageWahaUseCase
{
    private UserRepositoryInterface $userRepository;

    private array $scopes = [
        "1" => 'checkThePointsHitToday',
        "2" => 'hitPoint',
        //"3" => 'support'
    ];

    private array $options = [
        'checkThePointsHitToday' => [
            "1" => 'sendEmailPdf',
            "2" => 'returnToMenu'
        ],
        'hitPoint' => [
            "1" => 'addObservation',
            "2" => 'deletePoint',
            "3" => 'returnToMenu'
        ],
    ];

    public function __construct(
        UserRepositoryInterface $userRepository,
    ) {
        $this->userRepository = $userRepository;
    }

    public function webhookReceiveMessage(array $payload)
    {

        Log::info('MESSAGE RECEIVE', ['payload' => $payload]);

        $event = $payload['event'];
        if ($event == EventsWahaEnum::MESSAGE) {
            try {

                // Valid Payload receive
                $handledPayload = $this->validPayload($payload);
                if (!$handledPayload) {
                    Log::info('PAYLOAD NOT IS VALID', [
                        'payload' => $payload
                    ]);
                    return true;
                }

                // Quebra a string a partir do @ e coleta o primeiro elemento, que é o número do telefone
                $numberSearch = explode('@', $handledPayload['number'])[0];
                $user = $this->userRepository->getUserWithPhoneNumber($numberSearch);

                $validCompany = $this->validCompanyIsActive($user);
                if (!$validCompany) {
                    Log::info('COMPANY NOT ACTIVE', [
                        'user' => json_encode($user->toArray()),
                        'number' => $handledPayload['number']
                    ]);
                    sendMessageWhatsapp(
                        $handledPayload['number'],
                        $handledPayload['messageId'],
                        ["🙍🏻‍♂️ Olá! sua empresa não está ativa no momento. Por favor, entre em contato com o suporte."],
                        0
                    );
                    return false;
                }

                // Verifica se o usuário já se autenticou
                if ($user->getIsAuth()) {

                    // Se enviar uma opção valida
                    if (strtoupper($handledPayload['message']) == "MENU") {
                        Log::info('USER REQUEST MENU', [
                            'username' => $user->getName(),
                            'is_auth' => $user->getIsAuth()
                        ]);
                        $this->userRepository->updateScopeOfTheUser($user, "");
                        sendMessageWhatsapp($handledPayload['number'], $handledPayload['messageId'], [EventsWahaEnum::SCOPE], 0);
                        return true;
                    } elseif (!empty($user->getScope())) {

                        Log::info("USER CONTAIN SCOPE", [
                            'username' => $user->getName(),
                            'number' => $handledPayload['number'],
                            'scope' => $user->getScope()
                        ]);

                        $scopeCurrent = $user->getScope();
                        $messageObservation = "";
                        if (stripos($handledPayload['message'], ",")) {
                            $arrayMessage = explode(',', $handledPayload['message']);
                            $handledPayload['message'] = trim($arrayMessage[0]);
                            $messageObservation = trim($arrayMessage[1]);
                        }

                        if (array_key_exists($handledPayload['message'], $this->options[$scopeCurrent])) {
                            $option = $this->options[$scopeCurrent][$handledPayload['message']];
                            $this->dispatchOption($user, $scopeCurrent, $option, $handledPayload['number'], $handledPayload['messageId'], $messageObservation);
                            return true;
                        }

                        // Se não for uma opção válida, verifica se o usuário tem o escopo atual
                        Log::info('USER SCOPE NOT CONTAIN OPTION', [
                            'username' => $user->getName(),
                            'is_auth' => $user->getIsAuth(),
                            'scope' => $scopeCurrent,
                            'option' => $handledPayload['message']
                        ]);
                        throw new Exception('MESSAGE NOT UNDERSTOOD');
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

                    // Inicia o processo de autenticação enviando mensagem de boas vindas mais código OTP
                    if (!str_contains($handledPayload['message'], 'OTPU')) {
                        sendMessageWhatsapp($handledPayload['number'], $handledPayload['messageId'], [EventsWahaEnum::HI . $user->getName() . EventsWahaEnum::USERNOTAUTH], 2);
                    }

                    // Criar codigo de autenticação
                    $otp = "OTPU" . rand(100000, 999999);
                    $user->setOtpCode($otp);

                    // Atualiza o usuário com o código OTP
                    $this->userRepository->updateOTP($user);
                    Log::info('USER OTP UPDATE SUCCESS', [
                        'user' => json_encode($user->toArray()),
                        'otp' => $otp
                    ]);

                    // Envia código para o email
                    sendCodeOtpToEmail($user, $otp, 2);

                    return true;
                }
            } catch (UserNotFoundException $e) {
                Log::critical('USER NOT FOUND EXCEPTION', [
                    'number' => $handledPayload['number'],
                    'message' => $e->getMessage()
                ]);
                //sendMessageWhatsapp($handledPayload['number'], $handledPayload['messageId'], [EventsWahaEnum::USERNOTFOUND], 0);
                return false;
            } catch (CollectUserByPhoneException $e) {
                Log::critical('COLLECT USER BY PHONE EXCEPTION', [
                    'number' => $handledPayload['number'],
                    'message' => $e->getMessage()
                ]);
                sendMessageWhatsapp($handledPayload['number'], $handledPayload['messageId'], [EventsWahaEnum::MESSAGENOTUNDERSTOOD], 0);
                return false;
            } catch (UpdateScopeException $e) {
                Log::critical('UPDATED SCOPE EXCEPTION', [
                    'number' => $handledPayload['number'],
                    'message' => $e->getMessage()
                ]);
                sendMessageWhatsapp($handledPayload['number'], $handledPayload['messageId'], [EventsWahaEnum::MESSAGENOTUNDERSTOOD], 0);
                return false;
            } catch (Exception $e) {
                Log::critical('PROCESS ERROR', [
                    'number' => $handledPayload['number'] ?? "Not number",
                    'message' => $e->getMessage()
                ]);
                sendMessageWhatsapp($handledPayload['number'], $handledPayload['messageId'], [EventsWahaEnum::MESSAGENOTUNDERSTOOD], 0);
                return false;
            }
        }

        Log::info('EVENT WHATSAPP RECEIVE ERROR', [
            'event' => $event,
            'payload' => $payload
        ]);
        return false;
    }

    public function dispatchOption(UserEntity $user, string $scope, string $option = "", string $number, string $messageId, string $message = "")
    {
        Log::info(
            "OPTION SELECTED: ",
            [
                "username" => $user->getName(),
                "scope" => $scope,
                "option" => $option,
                "number" => $number,
                "messageId" => $messageId,
                'message' => $message
            ]
        );

        // Verifica se o escopo existe e se a opção é válida
        $scopeUseCase = OptionsFactory::getOptions($scope);

        if (!empty($option)) {
            $scopeUseCase->$option($user, $number, $messageId, $message);
            return true;
        }

        $scopeUseCase->receive($user, $number, $messageId);
        return true;
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

                sendMessageWhatsapp($number, $messageId, [EventsWahaEnum::AUTHSUCCESS], 0);
                sendMessageWhatsapp($number, $messageId, [EventsWahaEnum::MENU], 1);
                sendMessageWhatsapp($number, $messageId, [EventsWahaEnum::SCOPE], 2);

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
            sendMessageWhatsapp($number, $messageId, [EventsWahaEnum::CODEINVALIDRESEND], 0);
            return false;
        } catch (Exception $e) {
            Log::critical('USER REQUEST AUTH FAILED', [
                'username' => $user->getName(),
                'number' => $number,
                'message' => $e->getMessage()
            ]);
            sendMessageWhatsapp($number, $messageId, [EventsWahaEnum::MESSAGENOTUNDERSTOOD], 0);
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
            return false;
        }
    }

    public function validCompanyIsActive(UserEntity $user)
    {
        try {
            if (!$user->getCompany()) {
                Log::info('USER NOT HAVE COMPANY', [
                    'user' => json_encode($user->toArray())
                ]);
                return false;
            }

            if (!$user->getCompany()->isActive()) {
                Log::info('COMPANY NOT ACTIVE', [
                    'company' => json_encode($user->getCompany()->toArray())
                ]);
                return false;
            }

            return true;
        } catch (Exception $e) {
            Log::critical('VALID COMPANY IS ACTIVE ERROR', [
                'message' => $e->getMessage(),
                'user' => json_encode($user->toArray())
            ]);
            return false;
        }
    }
}
