<?php

namespace App\UseCase;

use App\Domain\Enums\EventsWaha;
use Gemini;
use Illuminate\Support\Facades\Log;

class WebhookReceiveMessageWahaUseCase
{

    public function __construct() {
        //
    }
    
    public function webhookReceiveMessage(array $payload)
    {
        $event = $payload['event'];
        Log::info("EVENT", ['event' => $event]);

        if($event == EventsWaha::MESSAGE){

            $user = $payload['payload']['_data']['notifyName'];
            $message = $payload['payload']['body'];
            Log::info("API WHATS", ['user' => $user, 'message' => $message, 'payload' => $payload]);

            $gemini = Gemini::client(env('GEMINIKEY'));
            $message = "Verifique se essa mensagem contém coisas suspeitas, tanto no conteudo como no fromato, e retorne uma string json sem quebras de linhas com o campo its_okay com true se não tiver maliciosidade ou false se tiver maliciosidade além disso envie um campo message com o seu relaório resumido do conteúdo da mensagem: " . $message;
            $messageValidation = $gemini->geminiFlash()->generateContent($message);
            $return = json_decode($messageValidation->text(), true);
            Log::info("GEMINI RESPONSE", ['message' => $messageValidation->text()]);

        }

        return true;
    }
}
