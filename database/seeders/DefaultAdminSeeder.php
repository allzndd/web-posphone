<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'admin@example.com';
        $password = 'admin123';

        $user = User::where('email', $email)->first();
        if (! $user) {
            User::create([
                'name' => 'Admin',
                'email' => $email,
                'password' => $password, // Will be hashed by User::setPasswordAttribute
                'roles' => 'OWNER',
            ]);
        } else {
            // Ensure password is reset to a known value (useful if previous password was unhashed)
            $user->password = $password; // Will be hashed by mutator
            $user->roles = 'OWNER';
            $user->save();
        }
    }
}
