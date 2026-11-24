<?php

namespace App\Mail;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PerformanceGuaranteeAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Contract $contract)
    {
    }

    public function build()
    {
        return $this->subject(__('performance_guarantee.mail_subject', ['name' => $this->contract->name]))
            ->view('emails.performance-guarantee-alert', [
                'contract' => $this->contract,
                'daysRemaining' => $this->contract->getDaysUntilPerformanceGuaranteeEnd(),
            ]);
    }
}
