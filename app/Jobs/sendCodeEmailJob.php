<?php

namespace App\Jobs;

use App\Mail\CodeMail;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class sendCodeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // número de tentativas
    public $maxExceptions = 3; // número máximo de exceções antes de falhar

    private string $email;
    private string $name;
    private string $code;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
      string $email,
      string $name,
      string $code
    )
    {
        $this->email = $email;
        $this->name = $name;
        $this->code = $code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info('Send code email job started', [
                'email' => $this->email,
                'name' => $this->name,
                'code' => $this->code
            ]);
    
            Mail::to($this->email)->send(new CodeMail($this->name, $this->code));

            Log::info('Send code email success', [
                'email' => $this->email,
                'name' => $this->name,
                'code' => $this->code
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Send code email error', [
                'email' => $this->email,
                'name' => $this->name,
                'code' => $this->code,
                'error' => $e->getMessage()
            ]);

            return false;
        };
    }
}
