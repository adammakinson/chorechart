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
     * @param $userId -  the id of the logged in user
     * @param $choreId - the chore id
     */
    public function update(Request $request, $assigneeId, $choreId)
    {
        //currently logged in user which would be the same as what's passed in the route.
        $user = auth()->user();

        $this->updateUserChore($assigneeId, $user, $choreId);

        if (Gate::allows('manage-chorechart')) {
            $chores = chores::all()->load('user', 'assigner' );
        } else {
            $chores = $user->chores()->get();
        }

        return $chores;
    }

    public function updateUserChore($assigneeId, $user, $choreId)
    {        
        $isAdmin = false;
        foreach($user->roles as $role) {
            if($role->role == "admin") {
                $isAdmin = true;
            }
        }

        $chore = UserChores::where('chore_id', $choreId)->first();

        if ($isAdmin && $user->id == $assigneeId) { // The currently logged in admin owns the chore
            $chore->inspection_ready = now();
            $chore->inspected_on = now();
            $chore->inspection_passed = true;
        } elseif ($isAdmin && $user->id != $assigneeId) { // The chore belongs to someone else...
            $chore->inspected_on = now();
            $chore->inspection_passed = true;
            $chore->pending = false;
        } else { // Not an admin user
            $chore->inspection_ready = now();
            $chore->pending = true;
        }

        $chore->save();
    }
}
