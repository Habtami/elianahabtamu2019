<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BackupNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $filename;

    public $backupTime;

    public $backupType;

    public $backupSize;

    public $createdBy;

    public $destinationPath;

    public function __construct($filename, $backupTime, $backupType, $backupSize, $createdBy, $destinationPath)
    {
        $this->filename = $filename;
        $this->backupTime = $backupTime;
        $this->backupType = $backupType;
        $this->backupSize = $backupSize;
        $this->createdBy = $createdBy;
        $this->destinationPath = $destinationPath;
    }

    public function build()
    {
        return $this->subject('Database Backup Created — '.$this->filename)
            ->view('emails.backup-notification');
    }
}
