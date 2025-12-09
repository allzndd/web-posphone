<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert Superadmin
        $superadminId = DB::table('pengguna')->insertGetId([
            'email' => 'superadmin@posphone.com',
            'email_is_verified' => 1,
            'password' => Hash::make('superadmin123'),
            'nama' => 'Super Administrator',
            'slug' => Str::slug('Super Administrator'),
            'role_id' => 1, // Superadmin role
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Owner 1
        $owner1Id = DB::table('pengguna')->insertGetId([
            'email' => 'owner@posphone.com',
            'email_is_verified' => 1,
            'password' => Hash::make('owner123'),
            'nama' => 'Owner Demo',
            'slug' => Str::slug('Owner Demo'),
            'role_id' => 2, // Owner role
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create owner entry
        DB::table('owner')->insert([
            'id' => 1,
            'pengguna_id' => $owner1Id,
        ]);

        // Insert Admin (staff from owner)
        DB::table('pengguna')->insert([
            'email' => 'admin@posphone.com',
            'email_is_verified' => 1,
            'password' => Hash::make('admin123'),
            'nama' => 'Admin Demo',
            'slug' => Str::slug('Admin Demo'),
            'role_id' => 3, // Admin role
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "\n";
        echo "================================\n";
        echo "Seeder berhasil dijalankan!\n";
        echo "================================\n";
        echo "Superadmin Account:\n";
        echo "Email: superadmin@posphone.com\n";
        echo "Password: superadmin123\n";
        echo "\n";
        echo "Owner Account:\n";
        echo "Email: owner@posphone.com\n";
        echo "Password: owner123\n";
        echo "\n";
        echo "Admin Account:\n";
        echo "Email: admin@posphone.com\n";
        echo "Password: admin123\n";
        echo "================================\n";
    }
}
