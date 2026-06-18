<?php

namespace App\Mail;

use App\Models\Application;
use App\Support\BilingualSubject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StageAdvancedToApplying extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: BilingualSubject::fromKey('emails.subjects.application_registered', $this->application->uid),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.applications.stage-applying',
            with: [
                'application' => $this->application,
            ],
        );
    }
}
