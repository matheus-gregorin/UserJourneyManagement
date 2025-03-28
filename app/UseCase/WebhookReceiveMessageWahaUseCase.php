<?php

namespace App\UseCase;

use App\Domain\Entities\UserEntity;
use App\Domain\Enums\EventsWahaEnum;
use App\Domain\Enums\ValidationIAEnum;
use App\Domain\HttpClients\ClientInterface;
use App\Domain\Repositories\UserRepositoryInterface;
use Gemini;
use Gemini\Client;
use Illuminate\Support\Facades\Log;

class WebhookReceiveMessageWahaUseCase
{

    private Client $IA;
    private ClientInterface $client;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        ClientInterface $client,
        UserRepositoryInterface $userRepository
    ) 
    {
        $this->IA = Gemini::client(env('GEMINIKEY'));
        $this->client = $client;
        $this->userRepository = $userRepository;
    }
    
    public function webhookReceiveMessage(array $payload)
    {

        Log::info('Message receive', ['payload' => $payload]);

        $event = $payload['event'];
        if($event == EventsWahaEnum::MESSAGE){

            $name = !empty($payload['payload']['_data']['notifyName']) ? $payload['payload']['_data']['notifyName'] : false;
            if($name != 'Gustavo Gregorin' && $name != 'math'){
                Log::info('Name not allowed', ['name' => $name]);
                return true;
            }
            Log::info('USER', ['user' => $payload['payload']['_data']['notifyName']]);

            $number = !empty($payload['payload']['from']) ? $payload['payload']['from'] : false;
            if(!$number){
                return true;
            }
            Log::info('NUMBER', ['number' => $payload['payload']['from']]);

            $numberSearch = explode('@', $number)[0];

            // Add try ctach para tratamento
            $user = $this->userRepository->getUserWithPhoneNumber($numberSearch);
            //

            $messageId = !empty($payload['payload']['id']) ? $payload['payload']['id'] : false;
            if(!$messageId){
                return true;
            }
            Log::info('DATA ID', ['messageId' => $payload['payload']['id']]);

            $message = !empty($payload['payload']['body']) ? $payload['payload']['body'] : false;
            if(!$message){
                return true;
            }
            Log::info('MESSAGE', ['message' => $payload['payload']['body']]);

            $validation = $this->validation($message);
            if($validation){
                $response = $this->generateResponse($user, $message);
                if($response){
                    // Envia para o client
                    $this->client->sendViewMessage($number, $messageId);
                    $this->client->startTyping($number);
                    sleep(5);
                    $this->client->stopTyping($number);
                    $this->client->sendResponse($number, $response);
                }
            }
        }

        return true;
    }

    public function validation(string $message)
    {
        $messageValidation = $this->IA->geminiFlash()->generateContent(ValidationIAEnum::VALIDCONTENT . $message);
        $validation = json_decode($messageValidation->text(), true);

        Log::info('Validation', ['validation' => $validation]);

        if($validation['its_okay']){
            return true;
        }
        return false;
    }

    public function generateResponse(UserEntity $user, string $message)
    {
        $responseMessage = $this->IA->geminiFlash()->generateContent(ValidationIAEnum::RESPONSE . $message . ' - Nome do usuário: ' . $user->getName());
        $response = json_decode($responseMessage->text(), true);

        Log::info('Response', ['response' => $response]);

        if(!is_null($response) && $response['its_okay']){
            return $response['response'];
        }
        return false;
    }
}
