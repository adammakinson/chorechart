<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\UserChoresController;
use Tests\TestCase;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\ChoresSeeder;
use Database\Seeders\UserChoreSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\RoleUserSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;


class UserChoresControllerTest extends TestCase
{
    // use DatabaseMigrations;
    use RefreshDatabase;
    
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_admin_can_assign_chore_to_self()
    {
        // $this->seed(ChoresSeeder::class);
        // $this->seed(UsersTableSeeder::class);
        // $this->seed(RolesSeeder::class);
        // $this->seed(RoleUserSeeder::class);
        // $this->seed(UserChoreSeeder::class);

        $user = User::find(1);

        $jsonData = [
            "1" => ["5"]
        ];

        $route = "api/user-chores";

        $response = $this->actingAs($user)->postJson($route, $jsonData);

        $response->assertStatus(200);
        
        $route = "api/user-chores/1";

        $response = $this->actingAs($user)->getJson($route);

        $response->assertJsonFragment(
            ['chore_id' => 5]
        );
    }

    public function test_we_can_get_user_chores()
    {
        // $this->seed(ChoresSeeder::class);
        // $this->seed(UsersTableSeeder::class);
        // $this->seed(RolesSeeder::class);
        // $this->seed(RoleUserSeeder::class);
        // $this->seed(UserChoreSeeder::class);

        $user = User::find(1);

        $route = "api/user-chores/1";

        $response = $this->actingAs($user)->getJson($route);

        $response->assertJsonFragment(
            ['user_id' => 1]
        );
    }

    public function test_we_can_update_user_chore()
    {
        // $this->seed(ChoresSeeder::class);
        // $this->seed(UsersTableSeeder::class);
        // $this->seed(RolesSeeder::class);
        // $this->seed(RoleUserSeeder::class);
        // $this->seed(UserChoreSeeder::class);

        $user = User::find(1);

        $route = "api/users/1/chores/1";

        $response = $this->actingAs($user)->put($route);

        $response->assertJsonFragment(
            ['user_id' => 1, 'chore_id' => 1, 'points_awarded' => true]
        );
    }

    public function test_we_can_delete_user_chore()
    {
        // $this->seed(ChoresSeeder::class);
        // $this->seed(UsersTableSeeder::class);
        // $this->seed(RolesSeeder::class);
        // $this->seed(RoleUserSeeder::class);
        // $this->seed(UserChoreSeeder::class);

        $user = User::find(1);

        
        $route = "api/user-chores/1";

        $response = $this->actingAs($user)->delete($route);

        $response->assertStatus(200);
    }
}
