<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public static function sendNotification($message)
    {
        if (!config('telegram.notifications_enabled', true)) {
            Log::info('Telegram notifications are disabled. Message not sent.');
            return false;
        }

        $token = config('services.telegram_bot.token', env('TELEGRAM_BOT_TOKEN'));
        $chatId = config('services.telegram_bot.chat_id', env('TELEGRAM_CHAT_ID'));

        if (!$token || !$chatId) {
            Log::warning('Telegram Token or Chat ID is missing.');
            return false;
        }

        $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);

        if (!$response->successful()) {
            Log::error('Telegram Error: ' . $response->body());
            return false;
        }

        return true;
    }

    public static function sendMessage($message, $threadId = null)
    {
        return self::sendNotification($message);
    }

    public static function setWebhook(string $url): bool
    {
        $token = config('services.telegram_bot.token', env('TELEGRAM_BOT_TOKEN'));

        $response = Http::post("https://api.telegram.org/bot{$token}/setWebhook", [
            'url' => $url,
        ]);

        if (!$response->successful()) {
            Log::error('Telegram setWebhook Error: ' . $response->body());
            return false;
        }

        return true;
    }

    public static function deleteWebhook(): bool
    {
        $token = config('services.telegram_bot.token', env('TELEGRAM_BOT_TOKEN'));

        $response = Http::post("https://api.telegram.org/bot{$token}/deleteWebhook");

        if (!$response->successful()) {
            Log::error('Telegram deleteWebhook Error: ' . $response->body());
            return false;
        }

        return true;
    }

    public static function getWebhookInfo(): ?array
    {
        $token = config('services.telegram_bot.token', env('TELEGRAM_BOT_TOKEN'));

        $response = Http::get("https://api.telegram.org/bot{$token}/getWebhookInfo");

        if (!$response->successful()) {
            Log::error('Telegram getWebhookInfo Error: ' . $response->body());
            return null;
        }

        return $response->json();
    }

    public static function isWebhookActive(): bool
    {
        $info = self::getWebhookInfo();

        if ($info === null || !isset($info['result'])) {
            return false;
        }

        return !empty($info['result']['url']);
    }
}
