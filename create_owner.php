<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$email = 'owner@gmail.com';
$password = 'owner123';

// Check if user already exists
$user = App\Models\User::where('email', $email)->first();

if ($user) {
    echo "✗ User already exists with email: {$email}\n";
    echo "  Updating password and role...\n";
    $user->password = $password;
    $user->roles = 'OWNER';
    $user->save();
    echo "✓ User updated successfully!\n";
} else {
    echo "Creating new owner account...\n";

    $user = App\Models\User::create([
        'name' => 'Owner',
        'email' => $email,
        'password' => $password,
        'roles' => 'OWNER',
        'phone' => '081234567890',
    ]);

    echo "✓ Owner account created successfully!\n";
}

echo "\n";
echo "=================================\n";
echo "Owner Account Credentials:\n";
echo "=================================\n";
echo "Email: {$email}\n";
echo "Password: {$password}\n";
echo "Role: OWNER\n";
echo "=================================\n";
