<?php

namespace App\Jobs;

use App\Domain\HttpClients\ClientHttpInterface;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ResponseMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // número de tentativas
    public $maxExceptions = 3; // número máximo de exceções antes de falhar

    private string $number;
    private string $messageId;
    private string $response;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        string $number,
        string $messageId,
        string $response
    )
    {
        $this->number = $number;
        $this->messageId = $messageId;
        $this->response = $response;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ClientHttpInterface $clientHttp)
    {
        Log::info('ResponseMessageJob', [
            'number' => $this->number,
            'messageId' => $this->messageId,
            'response' => $this->response
        ]);

        try {

            $clientHttp->sendViewMessage($this->number, $this->messageId);
            $clientHttp->startTyping($this->number);
            sleep(3);
            $clientHttp->stopTyping($this->number);
            $clientHttp->sendResponse($this->number, $this->response);

        } catch (Exception $e) {
            Log::error('Error ResponseMessageJob', [
                'number' => $this->number,
                'messageId' => $this->messageId,
                'response' => $this->response,
                'error' => $e->getMessage()
            ]);
        }
    }
}
