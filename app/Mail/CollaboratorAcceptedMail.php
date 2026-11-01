<?php

namespace App\Mail;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CollaboratorAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Employee $employee, public string $password)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Félicitations — votre embauche est confirmée',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.collaborator-accepted',
        );
    }
}
