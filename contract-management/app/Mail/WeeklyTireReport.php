<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyTireReport extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $stats;
    protected $gave;
    protected $returned;

    public function __construct($stats, $gave, $returned)
    {
        $this->stats = $stats;
        $this->gave = $gave;
        $this->returned = $returned;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Weekly Tire Report',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.weekly-tire-report',
            with: [
                'stats' => $this->stats,
                'gave' => $this->gave,
                'returned' => $this->returned,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
