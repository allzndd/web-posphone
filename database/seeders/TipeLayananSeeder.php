<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipeLayanan;

class TipeLayananSeeder extends Seeder
{
    public function run()
    {
        // Create Trial package
        TipeLayanan::updateOrCreate(
            ['slug' => 'trial'],
            [
                'nama' => 'Trial',
                'harga' => 0,
                'durasi' => 15,
                'durasi_satuan' => 'hari',
            ]
        );

        // You can add other packages here if needed
    }
}
