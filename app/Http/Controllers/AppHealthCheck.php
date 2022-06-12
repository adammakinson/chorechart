<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

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
     */
    public function index()
    {
        $appLoadable = true;
        $databaseIsConfigured = true;
        $initialUserIsSet = true;
        
        $envDbConfig = [
            'DB_CONNECTION',
            'DB_DATABASE',
            'DB_HOST',
            'DB_USERNAME',
            'DB_PASSWORD',
            'DB_PORT',
            'FORSWARD_DB_PORT'
        ];

        $initialAppUserConfig = [
            'INITIAL_USER',
            'INITIAL_USERNAME',
            'INITIAL_USER_PASS'
        ];

        // Really, this check needs to occur after
        // a check for the existence of the correct
        // database schema.
        foreach ($envDbConfig as $dbConfig) {
            if (!env($dbConfig, false)) {
                $databaseIsConfigured = false;
            }
        }
        
        // It's not this simple. I also need to check
        // if a user exists in the database.
        foreach ($initialAppUserConfig as $userConfig) {
            if (!env($userConfig, false)) {
                $initialUserIsSet = false;
            }
        }

        $message = 'App is not loadable';
        $appLoadable = $databaseIsConfigured && $initialUserIsSet;

        // $config = $_ENV;
        // $config = config('app.password_timeout');

        if ($appLoadable) {
            $message = 'App is loadable';
        }

        return $message;
    }
}
