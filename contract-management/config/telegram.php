<?php

return [
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'chat_id' => env('TELEGRAM_CHAT_ID'),
    'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),

    /*
    |--------------------------------------------------------------------------
    | Global Notification Toggle
    |--------------------------------------------------------------------------
    | Master switch for all Telegram notifications. When this is set to
    | false, TelegramService::sendMessage() will return early without
    | hitting the Telegram API. Useful for maintenance, testing, or
    | silencing notifications without removing code.
    |
    */
    'notifications_enabled' => env('TELEGRAM_NOTIFICATIONS_ENABLED', true),

    'notification_on_tire_return' => env('TELEGRAM_NOTIFY_RETURN', true),
    'notification_on_tire_give' => env('TELEGRAM_NOTIFY_GIVE', false),
    'license_expiry_notification_days' => env('TELEGRAM_LICENSE_EXPIRY_DAYS', 30),
];