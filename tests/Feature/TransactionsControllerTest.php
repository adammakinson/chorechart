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
    use RefreshDatabase;
    
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_transactions_ordered_by_creation_time_can_be_fetched()
    {        
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

    /**
     * @depends test_user_transactions_ordered_by_creation_time_can_be_fetched
     */
    public function test_transaction_store()
    {
        $user = User::find(1);

        $jsonData = [
            'user_id' => 10,
            'event' => 'spend',
            'user_points' => 25,
            'occurred_at' => Date('Y-m-d H:i:s', strtotime('now'))
        ];
        
        $route = "/api/users/$user->id/transactions";

        $response = $this->actingAs($user)->postJson($route, $jsonData);

        $response->assertStatus(200);
    }
}
