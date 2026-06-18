<?php

namespace App\Mail;

use App\Models\Subscriber;
use App\Support\BilingualSubject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeToClub extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Subscriber $subscriber) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: BilingualSubject::fromKey('emails.subjects.welcome_club'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.applications.welcome-to-club',
            with: [
                'subscriber' => $this->subscriber,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
