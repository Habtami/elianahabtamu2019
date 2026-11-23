<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogAuthenticationActivity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $type = null;
        
        if ($event instanceof Login) {
            $type = ActivityLog::TYPE_LOGIN;
        } elseif ($event instanceof Logout) {
            $type = ActivityLog::TYPE_LOGOUT;
        }

        if ($type) {
            ActivityLog::create([
                'activity_type' => $type,
                'user_id' => $event->user->id,
                'edited_by' => $event->user->name,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'severity' => ActivityLog::SEVERITY_INFO,
                'field_name' => $type,
            ]);
        }
    }
}
