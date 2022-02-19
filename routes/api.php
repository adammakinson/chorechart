<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ChoresController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\UserChoresController;
use App\Http\Controllers\TransactionsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::put('/update-credentials/{userid}', [AuthController::class, 'updateUserCredentials']);
Route::middleware('auth:sanctum')->apiResource('/users', UsersController::class);

/**
 * Resource route for chores that takes care of GET, POST PUT/PATCH, and DELETE
 * db table - chores
 */
Route::middleware('auth:sanctum')->apiResource('/chores', ChoresController::class);

/**
 * Assigns a chore to a user
 * db table - user_chores
 */
Route::middleware('auth:sanctum')->post('/user-chores', [UserChoresController::class, 'store']);

/**
 * Get all incomplete chores that are assigned to users
 */
Route::middleware('auth:sanctum')->get('/user-chores', [UserChoresController::class, 'getAllIncompleteChores']);

/**
 * Get a specific user's assigned chores
 */
Route::middleware('auth:sanctum')->get('/user-chores/{userId}', [UserChoresController::class, 'getUserChores']);

/**
 * Updates a chore assigned to a user
 * db table - user_chores
 * TODO: refactor this because it is more complicated than it needs to be.
 */
Route::middleware('auth:sanctum')->put('/users/{assigneeId}/chores/{choreId}', [UserChoresController::class, 'update']);

/**
 * Gets user transactions ordered by creation time
 * db table - transactions
 */
Route::middleware('auth:sanctum')->get('/users/{assigneeId}/transactions', [TransactionsController::class, 'getUserTransactionsOrderedByCreationTime']);

/**
 * Deletes a user-chore instance
 */
Route::middleware('auth:sanctum')->delete('/user-chores/{userChoreId}', [UserChoresController::class, 'deleteUsersChore']);

/**
 * Creates a new transaction. A transaction is created from the most recent one
 * IF a chore has been awarded points.
 * db table - transactions
 */
Route::middleware('auth:sanctum')->post('/users/{assigneeId}/transactions', [TransactionsController::class, 'store']);

/**
 * rewards resource route
 */
Route::middleware('auth:sanctum')->apiResource('/rewards', RewardController::class);