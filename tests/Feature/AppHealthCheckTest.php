<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\RoleUserSeeder;

class AppHealthCheckTest extends TestCase
{
    use DatabaseMigrations;

    
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_app_health_check_successful()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(RolesSeeder::class);
        $this->seed(RoleUserSeeder::class);

        $response = $this->getJson('/api/healthcheck');
        
        $response->assertJson(fn (AssertableJson $json) =>
            $json->where('message', 'App is loadable')
                 ->etc()
        );

        $response->assertStatus(200);
    }
}
