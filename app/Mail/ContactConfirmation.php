<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Accusé de réception envoyé au visiteur qui a écrit via le formulaire de contact
class ContactConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    public function __construct($contact)
    {
        $this->contact = $contact;
    }

    public function build()
    {
        return $this->subject('Nous avons bien reçu votre message')
            ->replyTo(config('mail.recipients.contact'), config('app.name'))
            ->text('emails.contact-client');
    }
}
