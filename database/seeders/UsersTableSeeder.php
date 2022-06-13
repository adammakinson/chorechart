<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Use command php artisan db:seed --class=UsersTableSeeder
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
            'name' => $_ENV['INITIAL_USER_NAME'],
            'username' => $_ENV['INITIAL_USER_USERNAME'],
            'email' => $_ENV['INITIAL_USER_EMAIL'],
            'password' => Hash::make($_ENV['INITIAL_USER_PASS']),
            'created_at' => now(),
            'updated_at' => now()
            ]
        ]);
    }
}
