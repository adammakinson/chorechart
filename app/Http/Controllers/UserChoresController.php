<?php

namespace App\Http\Controllers;

use App\Models\chores;
use App\Models\UserChores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserChoresController extends Controller
{
    // public function index()
    // {
    //     $allUserChores = UserChores::all();

    //     return $allUserChores;
    // }
    
    /**
     * Updates a chore entry specified by choreId in the user_chores table for 
     * user specified by userId and returns an updated chores list
     * 
     * @param $request - the PSR7 request
     * @param $assigneeId -  the id of the user from the route
     * @param $choreId - the chore id from the route
     * @return chores collection
     */
    public function update(Request $request)
    {
        return $this->updateUserChore($request->assigneeId, $request->choreId);
    }

    public function updateUserChore($assigneeId, $choreId)
    {
        $user = auth()->user();
        
        $isAdmin = false;
        foreach($user->roles as $role) {
            if($role->role == "admin" && $role->pivot->user_id == $user->id) {
                $isAdmin = true;
            }
        }

        $userChore = UserChores::where('chore_id', $choreId)->first();

        if ($isAdmin && $user->id == $assigneeId) { // The currently logged in admin owns the chore
            $userChore->inspection_ready = now();
            $userChore->inspected_on = now();
            $userChore->inspection_passed = true;
        } elseif ($isAdmin && $user->id != $assigneeId) { // The chore belongs to someone else...
            $userChore->inspected_on = now();
            $userChore->inspection_passed = true;
            $userChore->pending = false;
        } else { // Not an admin user
            $userChore->inspection_ready = now();
            $userChore->pending = true;
        }

        $userChore->save();

        return $userChore;
    }

    public function updateUserChorePointsAwarded($choreId)
    {
        $userChore = UserChores::where('chore_id', $choreId)->first();

        $userChore->points_awarded = true;

        $userChore->save();
    }

    public function getUserVisibleChores()
    {
        //currently logged in user which would be the same as what's passed in the route.
        $user = auth()->user();
        
        if (Gate::allows('manage-chorechart')) {
            $chores = chores::all()->load('user', 'assigner' );
        } else {
            $chores = $user->chores()->get();
        }

        return $chores;
    }
}
