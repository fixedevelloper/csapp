<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DevisSubmitted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $devis;

    public function __construct($devis)
    {
        $this->devis = $devis;
    }

    public function build()
    {
        return $this->subject('Nouvelle demande de devis')
            ->replyTo($this->devis->email, $this->devis->name)
            ->view('emails.devis');
    }
}
