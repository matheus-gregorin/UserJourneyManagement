<?php

use App\Jobs\ResponseMessageJob;
use Illuminate\Support\Facades\Log;

if (!function_exists('sendMessageWhatsapp')) {
    /**
     * Função helper para apresentar um log.
     *
     */
    function sendMessageWhatsapp(string $number, string $messageId, string $message, int $delay = 0)
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
            return false;
        }
    }
}
