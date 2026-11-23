<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TelegramDeleteWebhook extends Command
{
    protected $signature = 'telegram:delete-webhook';
    protected $description = 'Remove the Telegram bot webhook (disable webhook mode)';

    public function handle()
    {
        if (TelegramService::deleteWebhook()) {
            $this->info('Webhook removed successfully.');
        } else {
            $this->error('Failed to remove webhook. Check your bot token.');
        }

        return 0;
    }
}
