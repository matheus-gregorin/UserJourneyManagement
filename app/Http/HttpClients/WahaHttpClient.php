<?php

namespace App\Http\HttpClients;

use App\Domain\HttpClients\ClientHttpInterface;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WahaHttpClient implements ClientHttpInterface
{

    private Client $client;

    public function __construct() {
        $this->client = new Client(
            [
                'base_uri' => env('WAHAURL'),
                'timeout'  => 20,
                'headers'  => [
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json'
                ]
            ]
        );
    }

    public function sendViewMessage(string $number, string $messageId)
    {
        try {

            Log::info('Send View Message', ['messageId' => $messageId, 'number' => $number]);

            $response = $this->client->post('/api/sendSeen', [
                'json' => [
                    "chatId" => $number,
                    "messageId" => $messageId,
                    "participant" => null,
                    "session" => "default"
                ]
            ]);

            Log::info('Success Send View Message', [
                'messageId' => $messageId,
                'number' => $number, 
                'response' => json_decode($response->getBody()->getContents(), true)
            ]);
            return true;

        } catch (Exception $e) {
            Log::info('Error Send View Message', [
                'messageId' => $messageId,
                'number' => $number, 
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function startTyping(string $number)
    {
        try {

            Log::info('Send Start Typing', ['number' => $number]);

            $response = $this->client->post('/api/startTyping', [
                'json' => [
                    "chatId" => $number,
                    "session" => "default"
                ]
            ]);

            Log::info('Success Send Start Typing', [
                'number' => $number, 
                'response' => json_decode($response->getBody()->getContents(), true)
            ]);
            return true;

        } catch (Exception $e) {
            Log::info('Error Send Start Typing', [
                'number' => $number, 
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function stopTyping(string $number)
    {
        try {

            Log::info('Send Stop Typing', ['number' => $number]);

            $response = $this->client->post('/api/stopTyping', [
                'json' => [
                    "chatId" => $number,
                    "session" => "default"
                ]
            ]);

            Log::info('Success Send Stop Typing', [
                'number' => $number, 
                'response' => json_decode($response->getBody()->getContents(), true)
            ]);
            return true;

        } catch (Exception $e) {
            Log::info('Error Send Stop Typing', [
                'number' => $number, 
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function sendResponse(string $number, string $response): bool
    {

        try {

            Log::info('Send Message', ['number' => $number, 'message' => $response]);

            $response = $this->client->post('/api/sendText', [
                'json' => [
                        "chatId" => $number,
                        "reply_to" => null,
                        "text" => $response,
                        "linkPreview" => true,
                        "linkPreviewHighQuality" => false,
                        "session" => "default"
                ]
            ]);

            Log::info('Success Send Message', [
                'number' => $number, 
                'response' => json_decode($response->getBody()->getContents(), true)
            ]);
            return true;

        } catch (Exception $e) {
            Log::info('Error Send Message', [
                'number' => $number, 
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function sendButtons(string $number): bool
    {

        try {

            Log::info('Send Buttons', ['number' => $number]);

            $response = $this->client->post('/api/sendText', [
                'json' => [
                        "chatId" => $number . "@c.us",
                        "reply_to" => null,
                        "linkPreview" => true,
                        "linkPreviewHighQuality" => false,
                        "session" => "default"
                ]
            ]);

            Log::info('Success Send buttons', [
                'number' => $number, 
                'response' => json_decode($response->getBody()->getContents(), true)
            ]);
            return true;

        } catch (Exception $e) {
            Log::info('Error Send Buttons', [
                'number' => $number, 
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
