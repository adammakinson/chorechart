<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * This is a pretty awful way to do it.
         * I should be able to fetch the user and the role
         * and seed the role_user table with that.
         * 
         * This should "work" for this prototype stage though.
         */
        DB::table('role_user')->insert([
            [
            'role_id' => '1',
            'user_id' => '1',
            'created_at' => now(),
            'updated_at' => now()
            ],
            [
            'role_id' => '2',
            'user_id' => '2',
            'created_at' => now(),
            'updated_at' => now()
            ]
        ]);
    }
}
