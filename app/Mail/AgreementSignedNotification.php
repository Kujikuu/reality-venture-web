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

class AgreementSignedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: BilingualSubject::fromKey('emails.subjects.agreement_signed_admin', $this->application->uid),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.applications.agreement-signed-notification',
            with: [
                'application' => $this->application,
                'adminUrl' => url('/admin/applications/'.$this->application->id),
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (! $this->application->agreement_pdf_path) {
            return [];
        }

        return [
            \Illuminate\Mail\Mailables\Attachment::fromStorageDisk('local', $this->application->agreement_pdf_path)
                ->as("agreement-{$this->application->uid}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
