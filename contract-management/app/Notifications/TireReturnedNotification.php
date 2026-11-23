<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TireReturnedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $tireReturn;

    public function __construct($tireReturn)
    {
        $this->tireReturn = $tireReturn;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'serial_number' => $this->tireReturn->serial_number,
            'tire_type' => $this->tireReturn->tire_type,
            'driver_id' => $this->tireReturn->driver_id,
            'driver_name' => $this->tireReturn->driver->name ?? null,
            'current_km' => $this->tireReturn->current_kilometer,
            'km_difference' => $this->tireReturn->km_difference,
            'return_date' => $this->tireReturn->return_date->format('Y-m-d'),
        ];
    }
}
