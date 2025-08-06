<?php

use App\Jobs\sendCodeEmailJob;
use App\Jobs\SendHitsEmailJob;
use App\Jobs\SendHitsOfTheMounthEmailJob;
use App\Jobs\SendWhatsappMessageJob;
use Domain\Entities\UserEntity;
use Illuminate\Support\Facades\Log;

if (!function_exists('sendMessageWhatsapp')) {
    /**
     * Função helper para enviar mensagem no whatsapp.
     *
     */
    function sendMessageWhatsapp(string $number, string $messageId, array $messages, int $delay = 0, bool $sendView = true)
    {
        try {
            foreach ($messages as $message) {
                // Envia mensagem aqui
                SendWhatsappMessageJob::dispatch(
                    $number,
                    $messageId,
                    $message,
                    $sendView
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


if (!function_exists('sendCodeOtpToEmail')) {
    /**
     * Função helper para enviar código de autenticação no email.
     *
     */
    function sendCodeOtpToEmail(UserEntity $user, string $otp, int $delay = 0)
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
            return false;
        }
    }
}

if (!function_exists('sendPdfHitsTodayEmail')) {
    /**
     * Função helper para enviar pdf de pontos batidos hoje.
     *
     */

    function sendPdfHitsTodayEmail(UserEntity $user, array $hits, int $delay = 0)
    {
        try {
            // Envia o email aqui
            SendHitsEmailJob::dispatch(
                $user->getEmail(),
                $user->getName(),
                $hits
            )->delay(now()->addSeconds($delay));
            return true;
        } catch (Exception $e) {
            Log::info('SEND EMAIL ERROR', [
                'username' => $user->getName(),
                'email' => $user->getEmail(),
                'otp' => $hits,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}

if (!function_exists('sendPdfHitsOfTheMounthEmail')) {
    /**
     * Função helper para enviar pdf de pontos batidos hoje.
     *
     */

    function sendPdfHitsOfTheMounthEmail(UserEntity $user, array $hits, int $delay = 0)
    {
        try {
            // Envia o email aqui
            SendHitsOfTheMounthEmailJob::dispatch(
                $user->getEmail(),
                $user->getName(),
                $hits
            )->delay(now()->addSeconds($delay));
            return true;
        } catch (Exception $e) {
            Log::info('SEND EMAIL ERROR', [
                'username' => $user->getName(),
                'email' => $user->getEmail(),
                'otp' => $hits,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
