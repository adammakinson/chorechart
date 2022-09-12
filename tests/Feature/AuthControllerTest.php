<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_that_user_can_register()
    {
        
        $jsonData = [
            'name' => 'Joe Test',
            'username' => 'joeTest',
            'email' => 'joetest@yopmail.com',
            'password' => 'testpassword',
            'confirm_password' => 'testpassword'
        ];
        
        /**
         * How do I configure multiple connections in Laravel and switch between them?
         * I'd like to create a database specifically for testing. It might be in phpunit.xml
         */
        $response = $this->postJson('/api/register', $jsonData);

        $response->assertStatus(200);
    }

    /**
     * @depends test_that_user_can_register
     */
    public function test_user_can_login()
    {
        $jsonData = [
            'username' => 'joeTest',
            'password' => 'testpassword'
        ];

        $this->test_that_user_can_register();

        $response = $this->postJson('/api/login', $jsonData);

        // var_dump($response);

        $response->assertStatus(200);

        return $response->json();
    }

    /**
     * @depends test_user_can_login
     */
    public function test_user_can_update_credentials()
    {
        $jsonData = [
            'username' => 'joeTest',
            'password' => 'testpassword2',
            'confirm_password' => 'testpassword2'
        ];

        $loginResponse = $this->test_user_can_login();

        $response = $this->withHeaders([
            'authorization' => "Bearer " . $loginResponse['user']['access_token']
        ])->postJson('/api/update-credentials/' . $loginResponse['user']['id']);
    }
    
    /**
     * @depends test_user_can_login
     */
    public function test_user_can_update_info()
    {
        $jsonData = [
            'name' => 'Joe Tester',
            'username' => 'joeTester',
            'email' => 'joetester@yopmail.com',
        ];

        $loginResponse = $this->test_user_can_login();

        $response = $this->withHeaders([
            'authorization' => "Bearer " . $loginResponse['user']['access_token']
        ])->postJson('/api/update-credentials/' . $loginResponse['user']['id']);
    }
}
