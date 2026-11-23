<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContractExpiryWarning extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $recipient,
        public $expiring,
        public $expired
    ) {
    }

    public function build()
    {
        $total = $this->expiring->count() + $this->expired->count();

        return $this->subject('Contract Expiry Warning — ' . $total . ' contract(s) need your attention')
            ->view('emails.contract-expiry-warning');
    }
}
