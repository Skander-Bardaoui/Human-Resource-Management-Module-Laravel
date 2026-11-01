<?php

namespace App\Mail;

use App\Models\Candidate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidateHiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Candidate $candidate)
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
            view: 'emails.candidate-hired',
            text: 'emails.candidate-hired-text',
        );
    }
}
