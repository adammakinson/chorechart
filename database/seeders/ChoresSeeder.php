<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Use php artisan db:seed --class=ChoresSeeder
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('chores')->insert([
            ['chore' => 'Dishes', 'pointvalue' => '10', 'created_at' => now(), 'updated_at' => now()],
            ['chore' => 'Sweep the floor', 'pointvalue' => '10', 'created_at' => now(), 'updated_at' => now()],
            ['chore' => 'Feed the dog', 'pointvalue' => '10', 'created_at' => now(), 'updated_at' => now()],
            ['chore' => 'Wipe the counters', 'pointvalue' => '10', 'created_at' => now(), 'updated_at' => now()],
            ['chore' => 'Mow the lawn', 'pointvalue' => '10', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
