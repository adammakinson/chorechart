<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\chores;
use App\Models\UserChores;
use Illuminate\Http\Request;

class UserChoresController extends Controller
{
    // public function index()
    // {
    //     $allUserChores = UserChores::all();

    //     return $allUserChores;
    // }
    
    public function update(Request $request, $userId, $choreId)
    {
        // echo "updating inspection ready for chore $choreId for user $userId";

        $isAdmin = false;
        $user = auth()->user(); //currently logged in user which would be the same as what's passed in the route.
        $chore = UserChores::find($choreId);
        $assigneeId = $request->userId;

        foreach($user->roles as $role) {
            if($role->role == "admin") {
                $isAdmin = true;
            }
        }

        // dd($isAdmin, $userId, $assigneeId);

        if ($isAdmin && $userId == $assigneeId) { // The currently logged in admin owns the chore
            $chore->inspection_ready = now();
            $chore->inspected_on = now();
            $chore->inspection_passed = true;
        } elseif ($isAdmin && $userId != $assigneeId) { // The chore belongs to someone else...
            $chore->inspected_on = now();
            $chore->inspection_passed = true;
            $chore->pending = false;
        } else {
            $chore->inspection_ready = now();
            $chore->pending = true;
        }

        $chore->save();

        // return response('Successfully submitted chore for inspection', 200)->header('Content-Type', 'text/plain');
        return chores::all()->load('user', 'assigner' );
    }
}
