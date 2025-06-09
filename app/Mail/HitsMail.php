<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HitsMail extends Mailable
{
    use Queueable, SerializesModels;

    private string $username;
    private array $hits;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        string $username,
        array $hits
    ) {
        $this->username = $username;
        $this->hits = $hits;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Pontos batidos hoje',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'hits',
            with: [
                'username' => $this->username,
                'hits' => $this->hits,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
