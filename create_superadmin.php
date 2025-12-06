<?php

/**
 * Script untuk membuat user SUPERADMIN
 * Jalankan: php create_superadmin.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

// Data superadmin
$email = 'superadmin@gmail.com';
$password = 'superadmin123'; // Ganti dengan password yang Anda inginkan

// Cek apakah sudah ada
$existing = User::where('email', $email)->first();

if ($existing) {
    echo "User dengan email {$email} sudah ada!\n";
    echo "ID: {$existing->id}\n";
    echo "Name: {$existing->name}\n";
    echo "Role: {$existing->roles}\n";
    
    // Update role menjadi SUPERADMIN jika belum
    if ($existing->roles !== 'SUPERADMIN') {
        $existing->roles = 'SUPERADMIN';
        $existing->save();
        echo "\nRole berhasil diupdate menjadi SUPERADMIN!\n";
    }
} else {
    // Buat user baru
    $user = User::create([
        'name' => 'Super Admin',
        'email' => $email,
        'phone' => '08123456789',
        'password' => $password,
        'roles' => 'SUPERADMIN',
    ]);

    echo "User SUPERADMIN berhasil dibuat!\n";
    echo "Email: {$email}\n";
    echo "Password: {$password}\n";
    echo "\nSilakan login dengan kredensial di atas.\n";
}

echo "\n=== SELESAI ===\n";
