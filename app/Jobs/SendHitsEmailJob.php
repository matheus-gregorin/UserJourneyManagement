<?php

namespace App\Jobs;

use App\Mail\HitsMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendHitsEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // número de tentativas
    public $maxExceptions = 3; // número máximo de exceções antes de falhar

    private string $email;
    private string $name;
    private array $hits;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        string $email,
        string $name,
        array $hits
    ) {
        $this->email = $email;
        $this->name = $name;
        $this->hits = $hits;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info('Send hits email job started', [
                'email' => $this->email,
                'name' => $this->name,
                'code' => $this->hits
            ]);

            // 1. Gerar o conteúdo HTML da view PDF
            $html = view('hits', [
                'username' => $this->name,
                'hits' => $this->hits
            ])->render();

            $pdf = Pdf::loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdfContent = $pdf->output(); // Obtém o conteúdo do PDF

            Mail::to($this->email)->send(new HitsMail($this->name, $this->hits, $pdfContent));

            Log::info('Send hits email success', [
                'email' => $this->email,
                'name' => $this->name,
                'code' => $this->hits
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Send hits email error', [
                'email' => $this->email,
                'name' => $this->name,
                'code' => $this->hits,
                'error' => $e->getMessage()
            ]);

            return false;
        };
    }
}
