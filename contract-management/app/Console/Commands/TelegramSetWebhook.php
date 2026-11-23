<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TelegramSetWebhook extends Command
{
    protected $signature = 'telegram:set-webhook {url : The webhook URL}';
    protected $description = 'Set the Telegram bot webhook URL';

    public function handle()
    {
        $url = $this->argument('url');

        if (TelegramService::setWebhook($url)) {
            $this->info('Webhook set successfully.');
        } else {
            $this->error('Failed to set webhook. Check your bot token and URL.');
        }

        return 0;
    }
}
