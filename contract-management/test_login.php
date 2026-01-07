<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Testing login functionality...\n\n";

// Check if users exist
$users = User::all();
echo "Users in database:\n";
foreach($users as $user) {
    echo "- {$user->email} (Role: {$user->role}, Status: " . ($user->status ? 'Active' : 'Inactive') . ")\n";
}

echo "\nTesting password verification...\n";

// Test specific users
$testUsers = ['admin@epss.gov.et', 'habtamudoc87@gmail.com'];

foreach($testUsers as $email) {
    $user = User::where('email', $email)->first();
    if ($user) {
        echo "\nUser: {$user->email}\n";
        echo "Testing password 'admin123': " . (Hash::check('admin123', $user->password) ? 'VALID' : 'INVALID') . "\n";
        echo "Testing password 'epss@1234': " . (Hash::check('epss@1234', $user->password) ? 'VALID' : 'INVALID') . "\n";
        echo "Testing password 'password': " . (Hash::check('password', $user->password) ? 'VALID' : 'INVALID') . "\n";
    }
}

echo "\nDone.\n";
