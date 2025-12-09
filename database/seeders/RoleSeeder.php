<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'id' => 1,
                'nama' => 'Superadmin',
                'slug' => 'superadmin',
            ],
            [
                'id' => 2,
                'nama' => 'Owner',
                'slug' => 'owner',
            ],
            [
                'id' => 3,
                'nama' => 'Admin',
                'slug' => 'admin',
            ],
        ];

        DB::table('role')->insert($roles);
    }
}
