<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        DB::table('roles')->insert([
            'role' => 'user',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
