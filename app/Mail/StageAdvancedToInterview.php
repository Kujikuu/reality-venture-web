<?php

namespace App\Mail;

use App\Models\Application;
use App\Support\BilingualSubject;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StageAdvancedToInterview extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application,
        public ?string $scheduledAt = null,
        public ?string $meetingType = null,
        public ?string $meetingUrl = null,
        public ?string $meetingLocation = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: BilingualSubject::fromKey('emails.subjects.interview_invitation', $this->application->uid),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.applications.stage-interview',
            with: [
                'application' => $this->application,
                'scheduledAt' => $this->scheduledAt,
                'meetingType' => $this->meetingType,
                'meetingUrl' => $this->meetingUrl,
                'meetingLocation' => $this->meetingLocation,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
