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
        // return App::environment();

        $config = $_ENV;

        return $config;
    }
}
