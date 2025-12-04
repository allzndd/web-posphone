<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = [
            [
                'name' => 'Ganti LCD',
                'description' => 'Penggantian layar LCD untuk berbagai tipe HP',
                'price' => 250000,
                'duration' => 60,
                'status' => 'progress',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ganti Baterai',
                'description' => 'Penggantian baterai original dan KW',
                'price' => 150000,
                'duration' => 30,
                'status' => 'progress',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Service Software',
                'description' => 'Install ulang, update OS, hapus virus',
                'price' => 100000,
                'duration' => 45,
                'status' => 'progress',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ganti Touchscreen',
                'description' => 'Penggantian touchscreen / digitizer',
                'price' => 200000,
                'duration' => 60,
                'status' => 'progress',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Service Charging',
                'description' => 'Perbaikan port charging / IC charging',
                'price' => 175000,
                'duration' => 90,
                'status' => 'progress',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ganti Speaker',
                'description' => 'Penggantian speaker earpiece atau loudspeaker',
                'price' => 125000,
                'duration' => 45,
                'status' => 'progress',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Service Kamera',
                'description' => 'Perbaikan atau ganti kamera depan/belakang',
                'price' => 300000,
                'duration' => 60,
                'status' => 'progress',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ganti Mic',
                'description' => 'Penggantian microphone',
                'price' => 100000,
                'duration' => 30,
                'status' => 'progress',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Service Sinyal',
                'description' => 'Perbaikan masalah sinyal / network',
                'price' => 200000,
                'duration' => 120,
                'status' => 'progress',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Unlock Bootloader',
                'description' => 'Unlock bootloader dan root',
                'price' => 150000,
                'duration' => 60,
                'status' => 'progress',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('services')->insert($services);
    }
}
