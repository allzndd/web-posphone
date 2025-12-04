<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Storage;

class StorageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $storages = [
            '64GB',
            '128GB',
            '256GB',
            '512GB',
            '1TB',
        ];

        foreach ($storages as $storageName) {
            Storage::firstOrCreate(
                ['name' => $storageName],
                ['slug' => \Str::slug($storageName)]
            );
        }

        $this->command->info('Storages seeded successfully!');
    }
}
