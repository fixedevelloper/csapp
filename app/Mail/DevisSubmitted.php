<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DevisSubmitted extends Mailable
{
    public $devis;

    public function __construct($devis)
    {
        $this->devis = $devis;
    }

    public function build()
    {
        return $this->subject('Nouvelle demande de devis')
            ->view('emails.devis');
    }
}
