<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

/**
 * This is the new catch all route that sends everything to the SPA
 */
Route::get('/{any}', function() {
    // $user = auth()->user();

    // $token = $user->createToken('chores-access');

    // return response()->view('choresspa', ['authtoken' => $token->plainTextToken])->header('Authorization', 'Bearer ' . $token->plainTextToken);
    return response()->view('choresspa');
})->where('any', '.*');