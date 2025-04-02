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
use Exception;
use Gemini;
use Gemini\Client;
use Illuminate\Support\Facades\Log;

class WebhookReceiveMessageWahaUseCase
{

    private Client $IA;
    private UserRepositoryInterface $userRepository;
    private ClientHttpInterface $clientHttp;
    private array $themes = [];

    public function __construct(
        UserRepositoryInterface $userRepository,
        ClientHttpInterface $clientHttp
    ) 
    {
        $this->IA = Gemini::client(env('GEMINIKEY'));
        $this->IA->geminiFlash()->generateContent(
            ValidationIAEnum::SETUP
        );
        $this->userRepository = $userRepository;
        $this->clientHttp = $clientHttp;
    }
    
    public function webhookReceiveMessage(array $payload)
    {

        Log::info('Message receive', ['payload' => $payload]);

        $event = $payload['event'];
        if($event == EventsWahaEnum::MESSAGE){

            // Add try catch para tratamento
            try {

                $number = !empty($payload['payload']['from']) ? $payload['payload']['from'] : false;
                if(!$number){
                    throw new Exception('Number not found');
                }
                Log::info('NUMBER', ['number' => $number]);

                $messageId = !empty($payload['payload']['id']) ? $payload['payload']['id'] : false;
                if(!$messageId){
                    throw new Exception('Message ID not found');
                }
                Log::info('MESSAGE ID', ['messageId' => $messageId]);

                $message = !empty($payload['payload']['body']) ? $payload['payload']['body'] : false;
                if(!$message){
                    throw new Exception('Message not found');
                }
                Log::info('MESSAGE RECEIVE', ['messageReceive' => $message]);

                // Quebra a string a partir do @ e coleta o primeiro elemento, que é o número do tolefone
                $numberSearch = explode('@', $number)[0];
                $user = $this->userRepository->getUserWithPhoneNumber($numberSearch);

                            
                // Autenticação
                if(str_contains($message, 'OTP') && !$user->getIsAuth()){
                    // Valida e autentica o usuário, depois envia as demais opções //
                    return true;
                }

            } catch (UserNotFoundException $e){
                Log::critical('User not found', [
                    'number' => $number,
                    'message' => $e->getMessage()
                ]);
                $this->clientHttp->sendError($number, ValidationIAEnum::USERNOTFOUND);
                return true;

            } catch (CollectUserByPhoneException $e) {
                Log::critical('Collect user by Phone Exception', [
                    'number' => $number,
                    'message' => $e->getMessage()
                ]);
                $this->clientHttp->sendError($number, ValidationIAEnum::MESSAGENOTUNDERSTOOD);
                return true;

            } catch (Exception $e) {
                Log::critical('Validations error', [
                    'number' => $number ?? "Not number",
                    'message' => $e->getMessage()
                ]);
                $this->clientHttp->sendError($number, ValidationIAEnum::MESSAGENOTUNDERSTOOD);
                return true;

            }
            //

            $validation = $this->generateValidation($message);
            if($validation){

                if($user->getIsAuth()){

                    $response = $this->generateResponse($user, $message);
                    if($response){

                        // Envia a mensagem via Job
                        ResponseMessageJob::dispatch(
                            $number,
                            $messageId,
                            $response
                        )->delay(now()->addSeconds(5));
    
                        return true;
                    }

                } else {

                    // Envia a mensagem via Job
                    ResponseMessageJob::dispatch(
                        $number,
                        $messageId,
                        "Olá, " . $user->getName() . ", tudo bem? Você ainda não está autenticado. Para isso, enviei um codigo no seu email, por favor, verifique e me envie o código para que eu possa te ajudar. Caso não tenha recebido, entre em contato com seu supervisor ou acesse o nosso site para mais informações. (www.userManager.com.br)"
                    )->delay(now()->addSeconds(5));

                    // Criar codigo de autenticação
                    // $otp = rand(100000, 999999);
    
                    return true;
                }
            }
        }

        Log::info('Event whatsapp receive error', [
            'event' => $event,
            'payload' => $payload
        ]);

        return true;
    }

    public function generateScreening()
    {
        //
    }

    public function generateValidation(string $message)
    {
        $messageValidation = $this->talkIA(
            ValidationIAEnum::VALIDATION,
            $message
        );
        if(!$messageValidation){
            Log::info('Validation message not found', [
                'messageValidation' => $messageValidation,
                'message' => $message
            ]);
            return false;
        }
        $validation = json_decode($messageValidation->text(), true);

        Log::info('Validation', ['validation' => $validation]);

        if($validation['its_okay']){
            return true;
        }
        return false;
    }

    public function generateResponse(UserEntity $user, string $message)
    {
        $responseMessage = $this->talkIA(
            ValidationIAEnum::RESPONSE,
            $message,
            $user
        );
        if(!$responseMessage){
            Log::info('Response message not found', [
                'responseMessage' => $responseMessage,
                'user' => $user,
                'message' => $message
            ]);
            return false;
        }
        $response = json_decode($responseMessage->text(), true);

        Log::info('Response', ['response' => $response]);

        if(!is_null($response) && $response['its_okay']){
            return $response['response'];
        }
        return false;
    }

    public function talkIA(string $case, string $message, ?UserEntity $user = null)
    {
        
        $Ia = $this->IA->geminiFlash();

        switch ($case) {
            case ValidationIAEnum::VALIDATION:
                return $Ia->generateContent(
                    ValidationIAEnum::VALIDCONTENT . $message
                );
                break;
            case ValidationIAEnum::RESPONSE:
                return $Ia->generateContent(
                    ValidationIAEnum::GENERATERESPONSE . $message . ValidationIAEnum::THISUSERNAME . $user->getName()
                );
                break;
            default:
                Log::info('Case not found', ['case' => $case]);
                return false;
                break;
        }
    }
}
