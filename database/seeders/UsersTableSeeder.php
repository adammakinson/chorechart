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
            'name' => 'Adam Makinson',
            'username' => 'amakinson',
            'email' => 'adam.levi.makinson@gmail.com',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now()
            ],
            [
            'name' => 'Adam Makinson',
            'username' => 'cmakinson',
            'email' => 'cmakinson@yopmail.com',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now()
            ]
        ]);
    }
}
