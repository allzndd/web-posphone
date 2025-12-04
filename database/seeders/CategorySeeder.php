<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'iPhone iBox',
                'slug' => 'iphone-ibox',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'iPhone All Operator',
                'slug' => 'iphone-all-operator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'iPhone Inter',
                'slug' => 'iphone-inter',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('Categories created successfully!');
    }
}
