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
            ['chore' => 'Dishes', 'pointvalue' => '10'],
            ['chore' => 'Sweep the floor', 'pointvalue' => '10'],
            ['chore' => 'Feed the dog', 'pointvalue' => '10'],
            ['chore' => 'Wipe the counters', 'pointvalue' => '10'],
            ['chore' => 'Mow the lawn', 'pointvalue' => '10'],
        ]);
    }
}
