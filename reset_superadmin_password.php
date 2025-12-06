<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'superadmin@admin.com')->first();

if ($user) {
    $user->password = Hash::make('super123');
    $user->save();
    echo "Password untuk superadmin@admin.com berhasil direset menjadi: super123\n";
} else {
    echo "User superadmin@admin.com tidak ditemukan!\n";
}
