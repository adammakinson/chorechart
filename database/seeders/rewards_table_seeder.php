<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class rewards_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rewards')->insert([
            ['reward' => '30 minutes of Roblox time', 'point_value' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['reward' => 'One 30 minute episode', 'point_value' => 30, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
