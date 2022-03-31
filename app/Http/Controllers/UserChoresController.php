<?php

namespace App\Http\Controllers;

use App\Models\chores;
use App\Models\UserChores;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserChoresController extends Controller
{
    // public function index()
    // {
    //     $allUserChores = UserChores::all();

    //     return $allUserChores;
    // }

    public function getAllIncompleteChores()
    {
        if (Gate::allows('manage-chorechart')) {
            return UserChores::whereNull('inspection_passed')->get();
        }
    }

    public function getUserChores($userId)
    {
        $user = auth()->user();

        $userChores = $user->chores()->get();

        return $userChores;
    }

    public function store(Request $request)
    {
        
        if (Gate::allows('manage-chorechart')) {
            $assigner = auth()->user();
            
            $userAssignmentsData = $request->all();
            
            foreach($userAssignmentsData as $userId => $userAssignments) {
                $assignedTo = $userId;

                foreach ($userAssignments as $choreId) {

                    $chore = chores::find($choreId);

                    if (!empty($assignedTo)) {
                        $userChore = new UserChores;
                        $userChore->chore_id = $choreId;
                        $userChore->user_id = $assignedTo;
                        $userChore->chore = $chore->chore;
                        $userChore->pointvalue = $chore->pointvalue;
                        $userChore->assigner_id = $assigner->id;
                        $userChore->save();
                    }
                }
            }

            return response('OK', 200)->header('Content-Type', 'application/json');
        } else {
            return response('Forbidden', 403)->header('Content-Type', 'text/plain');
        }
    }
    
    /**
     * Updates a chore entry specified by choreId in the user_chores table for 
     * user specified by userId and returns an updated chores list
     * This updates the state of the chore when a user clicks the check
     * mark icon for the chore.
     * 
     * @param $request - the PSR7 request
     * @param $assigneeId -  the id of the user from the route
     * @param $choreId - the chore id from the route
     * @return userchores collection
     * TODO: simplify this to handle any update instead of just the approval state
     *      I think I still want a specific use case of updating approval state... need to think this one throug...
     */
    public function update(Request $request)
    {
        return $this->updateUserChore($request->assigneeId, $request->userChoreId);
    }

    public function updateUserChore($assigneeId, $userChoreId)
    {
        $user = auth()->user();
        
        $isAdmin = false;
        foreach($user->roles as $role) {
            if($role->role == "admin" && $role->pivot->user_id == $user->id) {
                $isAdmin = true;
            }
        }

        $userChore = UserChores::where('id', $userChoreId)->first();

        $chore = chores::where('id', $userChore->chore_id)->first();

        $userChore->chore = $chore->chore;
        $userChore->pointvalue = $chore->pointvalue;

        if ($isAdmin && $user->id == $assigneeId) { // The currently logged in admin owns the chore
            $userChore->inspection_ready = now();
            $userChore->inspected_on = now();
            $userChore->inspection_passed = true;
            $userChore->points_awarded = true;
        } elseif ($isAdmin && $user->id != $assigneeId) { // The chore belongs to someone else...
            $userChore->inspected_on = now();
            $userChore->inspection_passed = true;
            $userChore->points_awarded = true;
            $userChore->pending = false;
        } else { // Not an admin user
            $userChore->inspection_ready = now();
            $userChore->pending = true;
        }

        $userChore->save();

        return $userChore;
    }

    public function updateUserChorePointsAwarded(Request $request)
    {
        $userChore = UserChores::where('chore_id', $request->choreId)->first();

        $userChore->points_awarded = true;

        $userChore->save();

        return $userChore;
    }

    public function getUserVisibleChores()
    {
        //currently logged in user which would be the same as what's passed in the route.
        
        if (Gate::allows('manage-chorechart')) {
            $chores = chores::all()->load('user', 'assigner' );
        } else {
            $user = auth()->user();

            $currentUsersChores = [];

            $chores = $user->chores()->get();

            foreach($chores as $chore){
                if($chore->pivot['user_id'] == $user['id'] && $chore->pivot['inspection_passed'] != 1){
                    array_push($currentUsersChores, $chore);
                }
            }

            $chores = $currentUsersChores;
        }

        return $chores;
    }

    public function deleteUsersChore(Request $request, $userChoreId)
    {   
        if (Gate::allows('manage-chorechart')) {
            $userChore = UserChores::where('id', $userChoreId)->first();

            // The chore cannot be in progress. It has to be unstarted.
            if (empty($userChore['inspection_ready'])) {
                $userChore->delete();
                return response('OK', 200)->header('Content-Type', 'application/json');
            } else {
                return response('Forbidden - you cannot delete a users chore that has been started', 403)->header('Content-Type', 'text/plain');
            }
        } else {
            return response('Forbidden - You don\'t have permission to delet users chores', 403)->header('Content-Type', 'text/plain');
        }
    }
}
