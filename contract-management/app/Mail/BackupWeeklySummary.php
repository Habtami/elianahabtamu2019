<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BackupWeeklySummary extends Mailable
{
    use Queueable, SerializesModels;

    public $skippedCount;

    public $periodStart;

    public $periodEnd;

    public function __construct($skippedCount, $periodStart, $periodEnd)
    {
        $this->skippedCount = $skippedCount;
        $this->periodStart = $periodStart;
        $this->periodEnd = $periodEnd;
    }

    public function build()
    {
        return $this->subject('Weekly Backup Summary — '.$this->skippedCount.' skipped run(s)')
            ->view('emails.backup-weekly-summary');
    }
}
