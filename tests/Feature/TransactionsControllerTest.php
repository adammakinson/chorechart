<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\transactionSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;

class TransactionsControllerTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_transactions_ordered_by_creation_time_can_be_fetched()
    {
        $this->seed(UsersTableSeeder::class);
        $this->seed(transactionSeeder::class);
        
        $user = User::find(1);
        
        $route = "/api/users/$user->id/transactions";

        $response = $this->actingAs($user)->getJson($route);

        $response->assertJsonCount(1);

        $response->assertJsonFragment([
            'user_id' => 1,
            'event' => 'completed a chore',
            'user_points' => 10
        ]);

        $response->assertStatus(200);
    }
}
