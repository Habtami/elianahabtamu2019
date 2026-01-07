<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "Database Connection: " . DB::connection()->getDatabaseName() . "\n";
echo "Driver: " . DB::connection()->getDriverName() . "\n\n";

$email = 'habtamudoc87@gmail.com';
$user = User::where('email', $email)->first();

if ($user) {
    echo "User found: " . $user->email . "\n";
    echo "Status: " . ($user->status ? 'Active' : 'Inactive') . "\n";
    echo "Role: " . $user->role . "\n";
    
    $password = 'epss@1234';
    if (Hash::check($password, $user->password)) {
        echo "Password '{$password}' is CORRECT for this user.\n";
    } else {
        echo "Password '{$password}' is INCORRECT for this user.\n";
        echo "Hashed password in DB: " . $user->password . "\n";
    }
} else {
    echo "User '{$email}' NOT found in database.\n";
    
    echo "\nAll users in database:\n";
    $users = User::all();
    foreach($users as $u) {
        echo "- {$u->email} (Status: " . ($u->status ? 'Active' : 'Inactive') . ")\n";
    }
}
