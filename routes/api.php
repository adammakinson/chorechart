<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ChoresApiController;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::middleware('auth:sanctum')->put('/users/{assigneeId}/chores/{choreId}', [UserChoresController::class, 'update']);

Route::middleware('auth:sanctum')->put('/users/{assigneeId}/chores/{choreId}', function(Request $request) {
    
    $accumulatedPoints = 0;
    
    $userChoresController = new UserChoresController;
    $choresController = new ChoresApiController;
    $transactionsController = new TransactionsController;
    
    $chore = $choresController->getChoreById($request);
    $userChore = $userChoresController->update($request);
    $mostRecentTransaction = $transactionsController->getUsersMostRecentTransaction($request->assigneeId);
    
    if($mostRecentTransaction instanceof App\Models\transactions ) {
        $accumulatedPoints = $mostRecentTransaction->user_points;
    }

    if ($userChore->inspection_passed) {
        $transactionsController->store($request, $chore, $accumulatedPoints);
    }

    return $userChoresController->getUserVisibleChores();
});

Route::middleware('auth:sanctum')->apiResource('/users', UsersController::class);

Route::middleware('auth:sanctum')->apiResource('/chores', ChoresApiController::class);
// Route::apiResource('/chores', ChoresApiController::class);

// How does a user submit their chore for "inspection"?
// route: POST /chores/{id}?
// since creating a new chore is a POST to /chores to add to the collection,

 // What would a UserChoresController look like?
    // PUT /users/{id}/chores/{id}
        // This would get the UserChores model associated with user_id and chore_id
        // and update EITHER "inspection_ready" OR "inspected_on" + "inspection_passed"
