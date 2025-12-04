<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Color;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $colors = [
            'Black',
            'White',
            'Silver',
            'Gold',
            'Space Gray',
            'Blue',
            'Green',
            'Red',
            'Purple',
            'Pink',
            'Midnight',
            'Starlight',
        ];

        foreach ($colors as $colorName) {
            Color::firstOrCreate(
                ['name' => $colorName],
                ['slug' => \Str::slug($colorName)]
            );
        }

        $this->command->info('Colors seeded successfully!');
    }
}
