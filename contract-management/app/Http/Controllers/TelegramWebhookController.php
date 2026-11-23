<?php

namespace App\Http\Controllers;

use App\Models\TelegramLog;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $update = $request->all();

        if (!isset($update['message'])) {
            return response('OK', 200);
        }

        $message = $update['message'];
        $chatId = $message['chat']['id'] ?? null;
        $text = $message['text'] ?? '';
        $command = trim($text);

        $commands = [
            '/start' => "👋 Welcome to Tire Tracking Bot!\nUse /help for commands.",
            '/help' => "Available commands:\n/gives - Today's tire gives\n/returns - Today's tire returns\n/stats - General stats\n/status - System status\n/notifications - Check notification toggle",
            '/gives' => 'Recent tire gives: ' . (\App\Models\TireGive::whereDate('give_date', today())->count()),
            '/returns' => 'Recent tire returns: ' . (\App\Models\TireReturn::whereDate('return_date', today())->count()),
            '/stats' => "Total tires: " . \App\Models\Tire::count() . "\nDrivers: " . \App\Models\Driver::count(),
            '/status' => 'ሲስተም በጥሩ ሁኔታ እየሰራ ነው! ✅',
            '/notifications' => self::notificationsStatus(),
        ];

        $response = $commands[$command] ?? 'Unknown command. Use /help';

        TelegramLog::create([
            'chat_id' => $chatId,
            'message' => $text,
            'status' => TelegramLog::STATUS_SENT,
            'type' => 'webhook',
            'response' => $response,
        ]);

        TelegramService::sendNotification($response);

        return response('OK', 200);
    }

    private static function notificationsStatus(): string
    {
        $enabled = config('telegram.notifications_enabled', true);

        if ($enabled) {
            return '🔔 Notifications are ENABLED.';
        }

        return '🔕 Notifications are DISABLED.';
    }
}
