<?php

namespace App\Mail;

use App\Models\Application;
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
            subject: "Agreement Signed — {$this->application->uid}",
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
}
