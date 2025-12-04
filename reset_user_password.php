<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$email = 'abc@gmail.com';
$password = 'abc12345';

$user = App\Models\User::where('email', $email)->first();

if ($user) {
    $user->password = $password; // Will be hashed by User::setPasswordAttribute
    $user->save();
    echo "✓ Password successfully updated for: {$user->email}\n";
    echo "  Email: {$email}\n";
    echo "  New Password: {$password}\n";
} else {
    echo "✗ User not found with email: {$email}\n";
    echo "\nCreating new user...\n";

    $user = App\Models\User::create([
        'name' => 'ABC User',
        'email' => $email,
        'password' => $password,
        'roles' => 'ADMIN',
    ]);

    echo "✓ New user created successfully!\n";
    echo "  Email: {$email}\n";
    echo "  Password: {$password}\n";
}
