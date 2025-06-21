<?php

namespace App\Jobs;

use Domain\HttpClients\ClientHttpInterface;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsappMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // número de tentativas
    public $maxExceptions = 3; // número máximo de exceções antes de falhar

    private string $number;
    private string $messageId;
    private string $response;
    private bool $sendView = true;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        string $number,
        string $messageId,
        string $response,
        bool $sendView = true
    ) {
        $this->number = $number;
        $this->messageId = $messageId;
        $this->response = $response;
        $this->sendView = $sendView;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ClientHttpInterface $clientHttp)
    {
        Log::info('SendWhatsappMessageJob', [
            'number' => $this->number,
            'messageId' => $this->messageId,
            'response' => $this->response
        ]);

        try {

            if ($this->sendView) {
                $clientHttp->sendViewMessage($this->number, $this->messageId);
            }
            $clientHttp->startTyping($this->number);
            sleep(3);
            $clientHttp->stopTyping($this->number);
            $clientHttp->sendResponse($this->number, $this->response);
        } catch (Exception $e) {
            Log::error('Error SendWhatsappMessageJob', [
                'number' => $this->number,
                'messageId' => $this->messageId,
                'response' => $this->response,
                'error' => $e->getMessage()
            ]);
        }
    }
}
