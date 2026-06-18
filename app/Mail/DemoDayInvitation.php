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

class DemoDayInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application,
        public ?string $date = null,
        public ?string $location = null,
        public array $requirements = [],
        public ?string $meetingUrl = null,
        public bool $isOnline = false,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: BilingualSubject::fromKey('emails.subjects.demo_day_invitation', $this->application->uid),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.applications.demo-day-invitation',
            with: [
                'application' => $this->application,
                'date' => $this->date,
                'location' => $this->location,
                'requirements' => $this->requirements,
                'meetingUrl' => $this->meetingUrl,
                'isOnline' => $this->isOnline,
            ],
        );
    }
}
