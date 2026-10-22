<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Accusé de réception envoyé au visiteur qui a demandé un devis
class DevisConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $devis;

    public function __construct($devis)
    {
        $this->devis = $devis;
    }

    public function build()
    {
        return $this->subject('Nous avons bien reçu votre demande de devis')
            ->replyTo(config('mail.recipients.devis'), config('app.name'))
            ->view('emails.devis-client');
    }
}
