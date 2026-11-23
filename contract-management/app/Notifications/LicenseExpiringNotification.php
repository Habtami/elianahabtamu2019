<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LicenseExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $driver;
    protected $daysLeft;

    public function __construct($driver, $daysLeft)
    {
        $this->driver = $driver;
        $this->daysLeft = $daysLeft;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('License Expiry Alert')
            ->greeting('Hello,')
            ->line("Driver " . ($this->driver->name ?? 'Unknown') . "'s license (" . ($this->driver->license_number ?? 'N/A') . ")")
            ->line("will expire in {$this->daysLeft} days on {$this->driver->license_expiry->format('Y-m-d')}.")
            ->action('View Driver', url('/tire-tracking/drivers/' . $this->driver->id))
            ->line('Please take the necessary action.');
    }

    public function toArray($notifiable)
    {
        return [
            'driver_id' => $this->driver->id,
            'driver_name' => $this->driver->name,
            'license_number' => $this->driver->license_number,
            'license_expiry' => $this->driver->license_expiry,
            'days_left' => $this->daysLeft,
        ];
    }
}
