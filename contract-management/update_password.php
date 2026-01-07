<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'habtamudoc87@gmail.com')->first();
if ($user) {
    $user->password = Hash::make('epss@1234');
    $user->save();
    echo 'Password updated for habtamudoc87@gmail.com to epss@1234' . PHP_EOL;
} else {
    echo 'User not found' . PHP_EOL;
}
