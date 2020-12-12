<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
            'name' => 'Adam Makinson',
            'username' => 'amakinson',
            'password' => Hash::make('password')
        ]);
    }
}
