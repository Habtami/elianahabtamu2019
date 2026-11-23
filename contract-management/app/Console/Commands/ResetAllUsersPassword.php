<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAllUsersPassword extends Command
{
    protected $signature = 'users:reset-password';

    protected $description = 'Reset every user password to the configured default password and force a change on next login';

    public function handle(): int
    {
        $defaultPassword = config('app.default_password');

        $count = User::query()->update([
            'password' => Hash::make($defaultPassword),
            'must_change_password' => true,
        ]);

        $this->info("{$count} user(s) reset to the default password ({$defaultPassword}). They must change it on next login.");

        return self::SUCCESS;
    }
}
