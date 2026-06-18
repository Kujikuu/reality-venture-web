<?php

namespace App\Mail;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Support\BilingualSubject;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application,
        public ApplicationStatus $status,
        public string $statusLabel,
        public string $statusLabelAr,
        public ?string $note = null,
        public bool $rvClubInvite = false
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: BilingualSubject::fromKey('emails.subjects.status_update', $this->application->uid),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.applications.status-update',
            with: [
                'application' => $this->application,
                'status' => $this->status,
                'statusLabel' => $this->statusLabel,
                'statusLabelAr' => $this->statusLabelAr,
                'note' => $this->note,
                'rvClubInvite' => $this->rvClubInvite,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
