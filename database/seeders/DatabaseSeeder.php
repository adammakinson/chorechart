<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Seed the database with the bare minimum required data
         */
        $this->call([
            UsersTableSeeder::class,
            RolesSeeder::class,
            RoleUserSeeder::class,
            ChoresSeeder::class,
            rewards_table_seeder::class,
            UserChoreSeeder::class,
            HomeImageSeeder::class,
            FaviconsSeeder::class
        ]);
    }
}
