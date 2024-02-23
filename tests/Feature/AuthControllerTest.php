<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    /**
     * Private property $seed is set to true on the TestCase class
     * and that is used along with this trait to run all seeders
     * before running the tests
     */
    use RefreshDatabase;

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
