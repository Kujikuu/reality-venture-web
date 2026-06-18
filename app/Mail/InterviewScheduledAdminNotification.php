<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InterviewScheduledAdminNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application,
        public ?string $scheduledAt = null,
        public ?string $meetingType = null,
        public ?string $meetingUrl = null,
        public ?string $meetingLocation = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Interview Scheduled — {$this->application->uid}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.applications.interview-scheduled-admin',
            with: [
                'application' => $this->application,
                'scheduledAt' => $this->scheduledAt,
                'meetingType' => $this->meetingType,
                'meetingUrl' => $this->meetingUrl,
                'meetingLocation' => $this->meetingLocation,
                'adminUrl' => url('/admin/applications/'.$this->application->id),
            ],
        );
    }
}
