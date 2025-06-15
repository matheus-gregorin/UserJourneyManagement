<?php

use App\Jobs\SendWhatsappMessageJob;
use Illuminate\Support\Facades\Log;

if (!function_exists('sendMessageWhatsapp')) {
    /**
     * Função helper para apresentar um log.
     *
     */
    function sendMessageWhatsapp(string $number, string $messageId, array $messages, int $delay = 0)
    {
        try {
            foreach ($messages as $message) {
                // Envia mensagem aqui
                SendWhatsappMessageJob::dispatch(
                    $number,
                    $messageId,
                    $message
                )->delay(now()->addSeconds($delay));

                Log::info('SEND MESSAGE SUCCESS', [
                    'number' => $number,
                    'messageId' => $messageId,
                    'message' => $message
                ]);
            }

            return true;
        } catch (Exception $e) {
            Log::info('SEND MESSAGE ERROR', [
                'number' => $number,
                'messageId' => $messageId,
                'message' => $messages,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
