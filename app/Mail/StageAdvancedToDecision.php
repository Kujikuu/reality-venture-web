<?php

namespace App\Mail;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Support\BilingualSubject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StageAdvancedToDecision extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application,
        public ApplicationStatus $status,
        public ?string $note = null,
        public bool $rvClubInvite = false,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: BilingualSubject::fromKey('emails.subjects.application_decision', $this->application->uid),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.applications.stage-decision',
            with: [
                'application' => $this->application,
                'status' => $this->status,
                'statusLabel' => $this->status->label(),
                'statusLabelAr' => $this->status->labelAr(),
                'note' => $this->note,
                'rvClubInvite' => $this->rvClubInvite,
            ],
        );
    }
}
