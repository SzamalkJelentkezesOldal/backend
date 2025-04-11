<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PortfolioEldontesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $jelentkezo;
    public $elfogadottPortfoliok;
    public $elutasitottPortfoliok;
    public $registrationLink;

    /**
     * Create a new message instance.
     */
    public function __construct($jelentkezo, $elfogadottPortfoliok, $elutasitottPortfoliok, $registrationLink)
    {
        $this->jelentkezo = $jelentkezo;
        $this->elfogadottPortfoliok = $elfogadottPortfoliok;
        $this->elutasitottPortfoliok = $elutasitottPortfoliok;
        $this->registrationLink = $registrationLink;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Összegző értesítés a portfólió értékelésről',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'portfolio_osszegzo',
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
