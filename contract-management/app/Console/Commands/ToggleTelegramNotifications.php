<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ToggleTelegramNotifications extends Command
{
    protected $signature = 'telegram:toggle-notifications {state? : Enable or disable (enable|disable|on|off)}';
    protected $description = 'Toggle Telegram notifications on or off globally';

    public function handle()
    {
        $state = $this->argument('state');
        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            $this->error('.env file not found.');
            return 1;
        }

        $contents = File::get($envPath);
        $key = 'TELEGRAM_NOTIFICATIONS_ENABLED';

        if (!preg_match('/^' . $key . '=.*/m', $contents)) {
            $this->error($key . ' not found in .env file.');
            return 1;
        }

        if ($state === null) {
            $current = $this->readEnvValue($contents, $key);
            $newState = $current === 'true' ? 'false' : 'true';
        } else {
            $lower = strtolower($state);
            if (in_array($lower, ['enable', 'on', 'true', '1'])) {
                $newState = 'true';
            } elseif (in_array($lower, ['disable', 'off', 'false', '0'])) {
                $newState = 'false';
            } else {
                $this->error("Invalid state. Use: enable|disable|on|off|true|false");
                return 1;
            }
        }

        $contents = preg_replace(
            '/^' . $key . '=.*/m',
            $key . '=' . $newState,
            $contents
        );

        File::put($envPath, $contents);

        if ($newState === 'true') {
            $this->info('Telegram notifications ENABLED.');
        } else {
            $this->info('Telegram notifications DISABLED.');
        }

        return 0;
    }

    private function readEnvValue(string $contents, string $key): ?string
    {
        if (preg_match('/^' . $key . '=(.*)$/m', $contents, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }
}
