<?php

namespace App\Mail;

use App\Models\Application;
use App\Support\BilingualSubject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgreementSignedConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: BilingualSubject::fromKey('emails.subjects.agreement_signed', $this->application->uid),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.applications.agreement-signed-confirmation',
            with: [
                'application' => $this->application,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        if (! $this->application->agreement_pdf_path) {
            return [];
        }

        return [
            Attachment::fromStorageDisk('local', $this->application->agreement_pdf_path)
                ->as("agreement-{$this->application->uid}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
