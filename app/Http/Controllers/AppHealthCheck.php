<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class AppHealthCheck extends Controller
{
    /**
     * Check the health of the application
     *
     * What really is the minimum we need in order for the application
     * to run?
     * 
     * A set up database configuration.
     * Check that the tables exist.
     * Perhaps check if one user exists?
     * 
     * There is a potential security issue here. Say we have the .env
     * file filled out appropriately and the appropriate database schema
     * but we have no users. In this case, do we want to "assume" the
     * app hasn't been set up yet and prompt the user to create the first
     * "admin" user? After that initial setup phase, how do we protect
     * the user creation route so that people are not able to create
     * an admin account whenever they want?
     * 
     * What if I moved the initial user's username and pwd to .env
     * and then if the appropriate database information and user info
     * doesn't exist, we can't run the migrations or seeders? The two
     * pertinent seeders would be the users seeder and the user-roles
     * seeder.
     * 
     * Do we want checking the app health and setting app config to
     * be separate things? Do we even want a user interface for setting
     * .env variables???
     * 
     * We need to check if an admin user exists too.
     * 
     * 1. Run a query checking for the database specified in the .env file
     */
    public function index()
    {
        $appLoadable = false;
        $message = 'App is not loadable';
        
        $databaseIsConfigured = $this->databaseIsConfigured();
        if (!$databaseIsConfigured) {
            $message = 'Looks like you haven\'t configured the database. Check your .env file to ensure your database connection is correct.';
            return $message;
        }

        $schemaIsCorrect = $this->databaseSchemaIsCorrect();
        if (!$schemaIsCorrect) {
            $message = 'The database is not set up right. Did you run migrations?';
            return $message;
        }
        
        // So, first we need to check the users table for the existence of the
        // users that should be put in place by the seeders. At minimum we need
        // users and roles, I believe.
        $initialUserIsSet = $this->initialAppUserIsConfigured();
        if (!$initialUserIsSet) {
            $message = 'You have not set your initial admin user. Check your .env file';
            return $message;
        }

        $minimumRequiredSeedersRun = $this->initialAppUserExistsInDb() && $this->rolesExist() && $this->userRolesAssigned();
        if (!$minimumRequiredSeedersRun) {
            $message = 'You don\'t have your initial user completely set up. you may need to run your RoleSeeder and RoleUserSeeder.';
        }

        $appLoadable = $databaseIsConfigured && $schemaIsCorrect && $initialUserIsSet && $minimumRequiredSeedersRun;

        if ($appLoadable) {
            $message = 'App is loadable';
        }

        return $message;
    }

    private function databaseIsConfigured()
    {
        $databaseIsConfigured = true;

        $envDbConfig = [
            'DB_CONNECTION',
            'DB_DATABASE',
            'DB_HOST',
            'DB_USERNAME',
            'DB_PASSWORD',
            'DB_PORT',
            'FORSWARD_DB_PORT'
        ];

        foreach ($envDbConfig as $dbConfig) {
            if (!env($dbConfig, false)) {
                $databaseIsConfigured = false;
                break;
            }
        }

        return $databaseIsConfigured;
    }

    /**
     * Check that all tables exist in the database
     */
    private function databaseSchemaIsCorrect(): bool
    {
        $schemaIsCorrect = true;

        // It'd be nice if running migrations 
        $dbTables = [
            'chores',
            'failed_jobs',
            'images',
            'migrations',
            'password_resets',
            'personal_access_tokens',
            'rewards',
            'role_user',
            'roles',
            'transactions',
            'user_chores',
            'users'
        ];

        foreach ($dbTables as $table) {
            if (!Schema::hasTable($table)) {
                $schemaIsCorrect = false;
                break;
            }
        }

        return $schemaIsCorrect;
    }

    private function initialAppUserIsConfigured()
    {
        $initialUserIsSet = true;
        
        $initialAppUserConfig = [
            'INITIAL_USER_NAME',
            'INITIAL_USER_USERNAME',
            'INITIAL_USER_PASS',
            'INITIAL_USER_EMAIL'
        ];

        foreach ($initialAppUserConfig as $userConfig) {
            if (!env($userConfig, false)) {
                $initialUserIsSet = false;
                break;
            }
        }

        return $initialUserIsSet;
    }

    private function initialAppUserExistsInDb()
    {
        $initialUser = DB::table('users')
                                ->where('username', $_ENV['INITIAL_USER_USERNAME'])
                                ->first();

        return $initialUser->username == $_ENV['INITIAL_USER_USERNAME'];
    }

    private function rolesExist()
    {
        $roles = DB::table('roles')
                    ->select('role')
                    ->get();

        $roleCount = $roles->count();

        return $roleCount > 0;
    }
    
    private function userRolesAssigned()
    {
        $initialUserRoles = DB::table('users')
                                ->join('role_user', 'users.id', '=', 'role_user.user_id')
                                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                                ->where('users.username', $_ENV['INITIAL_USER_USERNAME'])
                                ->select('roles.role')
                                ->get();

        return count($initialUserRoles) > 0;
    }
}
