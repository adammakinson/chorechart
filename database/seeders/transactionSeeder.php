<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class transactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transactions')->insert([
            [
            'user_id' => '1',
            'event' => 'completed a chore',
            'user_points' => '10',
            'occurred_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
            ]
        ]);
    }
}
