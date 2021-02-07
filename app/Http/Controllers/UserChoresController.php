<?php

namespace App\Http\Controllers;

use App\Models\User;
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
    
    public function update(Request $request, $userId, $choreId)
    {

        $isAdmin = false;
        $user = auth()->user(); //currently logged in user which would be the same as what's passed in the route.
        $chore = UserChores::where('chore_id', $choreId)->first();

        $assigneeId = $request->userId;

        foreach($user->roles as $role) {
            if($role->role == "admin") {
                $isAdmin = true;
            }
        }

        if ($isAdmin && $userId == $assigneeId) { // The currently logged in admin owns the chore
            $chore->inspection_ready = now();
            $chore->inspected_on = now();
            $chore->inspection_passed = true;
        } elseif ($isAdmin && $userId != $assigneeId) { // The chore belongs to someone else...
            $chore->inspected_on = now();
            $chore->inspection_passed = true;
            $chore->pending = false;
        } else { // Not an admin user
            $chore->inspection_ready = now();
            $chore->pending = true;
        }

        $chore->save();

        if (Gate::allows('manage-chorechart')) {
            $chores = chores::all()->load('user', 'assigner' );
        } else {
            $user = auth()->user();
            $chores = $user->chores()->get();
        }

        return $chores;
    }
}
