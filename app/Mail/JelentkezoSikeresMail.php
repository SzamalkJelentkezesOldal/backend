<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JelentkezoSikeresMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nev;
    public $elfogadottSzakok;

    /**
     * Create a new message instance.
     */
    public function __construct($nev, $elfogadottSzakok)
    {
        $this->nev = $nev;
        $this->elfogadottSzakok = $elfogadottSzakok;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sikeres felvétel értesítés',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'jelentkezo_sikeres',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
} 