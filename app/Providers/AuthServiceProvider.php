<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        /**
         * TODO: I need to create roles for the user and fix
         * this gate to use the role::auth (to be created)
         * 
         * really should be manage-chorechart instead of view-chorechart...
         */
        Gate::define('view-chorechart', function(User $user){
            $authorized = false;
            $validRoles = [
                'admin',
                'user'
            ];

            foreach($user->roles as $role) {
                if(in_array($role->role, $validRoles)) {
                    $authorized = true;
                }
            }

            return $authorized;
        });
        
        Gate::define('manage-chorechart', function(User $user){
            $authorized = false;

            foreach($user->roles as $role) {
                if($role->role == 'admin') {
                    $authorized = true;
                }
            }

            return $authorized;
        });
    }
}
