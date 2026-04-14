use App\Enums\ApplicationStatus;
use App\Models\Application;
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
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "تحديث على طلبك | Application Decision — {$this->application->uid}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.stage-decision',
            with: [
                'application' => $this->application,
                'status' => $this->status,
            ],
        );
    }
}
