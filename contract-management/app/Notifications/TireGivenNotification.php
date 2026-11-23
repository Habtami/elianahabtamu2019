<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TireGivenNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $tireGive;

    public function __construct($tireGive)
    {
        $this->tireGive = $tireGive;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'tire_id' => $this->tireGive->tire_id,
            'tire_serial' => $this->tireGive->tire->serial_number ?? null,
            'tire_type' => $this->tireGive->tire->tire_type ?? null,
            'driver_id' => $this->tireGive->driver_id,
            'driver_name' => $this->tireGive->driver->name ?? null,
            'kilometer' => $this->tireGive->given_kilometer,
            'give_date' => $this->tireGive->give_date->format('Y-m-d'),
        ];
    }
}
