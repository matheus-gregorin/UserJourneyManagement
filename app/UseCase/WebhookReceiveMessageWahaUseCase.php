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

            $number = !empty($payload['payload']['from']) ? $payload['payload']['from'] : false;
            if(!$number){
                return true;
            }
            Log::info('NUMBER', ['number' => $number]);

            // Quebra a string a partir do @ e coleta o primeiro elemento, que é o número do tolefone
            $numberSearch = explode('@', $number)[0];

            // Add try catch para tratamento
            try {

                $user = $this->userRepository->getUserWithPhoneNumber($numberSearch);

            } catch (UserNotFoundException $e){
                Log::critical('User not found', [
                    'number' => $number,
                    'message' => $e->getMessage()
                ]);
                $this->clientHttp->sendError(ValidationIAEnum::USERNOTFOUND);
                return true;

            } catch (CollectUserByPhoneException $e) {
                Log::critical('Collect user by Phone Exception', [
                    'number' => $number,
                    'message' => $e->getMessage()
                ]);
                $this->clientHttp->sendError(ValidationIAEnum::MESSAGENOTUNDERSTOOD);
                return true;

            } catch (Exception $e) {
                Log::critical('Get user error', [
                    'number' => $number,
                    'message' => $e->getMessage()
                ]);
                $this->clientHttp->sendError(ValidationIAEnum::MESSAGENOTUNDERSTOOD);
                return true;

            }
            //

            $messageId = !empty($payload['payload']['id']) ? $payload['payload']['id'] : false;
            if(!$messageId){
                return true;
            }
            Log::info('MESSAGE ID', ['messageId' => $messageId]);

            $message = !empty($payload['payload']['body']) ? $payload['payload']['body'] : false;
            if(!$message){
                return true;
            }
            Log::info('MESSAGE RECEIVE', ['messageReceive' => $message]);

            $validation = $this->generateValidation($message);
            if($validation){
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
                    ValidationIAEnum::GENERATERESPONSE . $message . ValidationIAEnum::THISUSERNAME . $user->getName() . " E me fale quem é você e qual o seu proposito de trabalho nesse app."
                );
                break;
            default:
                Log::info('Case not found', ['case' => $case]);
                return false;
                break;
        }
    }
}
