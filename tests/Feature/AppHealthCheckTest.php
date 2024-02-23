<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;

class AppHealthCheckTest extends TestCase
{   
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_app_health_check_successful()
    {
        $response = $this->getJson('/api/healthcheck');
        
        $response->assertJson(fn (AssertableJson $json) =>
            $json->where('message', 'App is loadable')
                 ->etc()
        );

        $response->assertStatus(200);
    }
}
