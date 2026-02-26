<?php

namespace App\Mail;

use App\Models\ConsultantProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsultantApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public ConsultantProfile $profile) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Consultant Profile Has Been Approved!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.consultants.approved',
            with: [
                'profile' => $this->profile,
                'name' => $this->profile->user->name,
            ],
        );
    }
}
