<?php
/**
 * I'm wondering if all I need is one main controller
 * and certain 
 */
namespace App\Http\Controllers;

use App\Models\chores;
use App\Models\User;
use App\Models\UserChores;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ChoresApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * All users should be able to view basic chores
     * NOTE: we are using the relationship here to eager-load chores per user
     * Take a look at the user Model at the relationship to see the extra Pivot
     * table fields we are including from the UserChores Model
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        if (Gate::allows('manage-chorechart')) {

            $chores = chores::all()->load('user', 'assigner');
    
            return $chores;

        } else if (Gate::allows('view-chorechart')) {

            $user = auth()->user();
            $chores = $user->chores()->get();
    
            return $chores;
        } else {
            return response('Forbidden', 404)->header('Content-Type', 'text/plain');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * Admin users only
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Gate::allows('manage-chorechart')) {

            $chore = new Chores;
            $chore->chore = $request->chore;
            $chore->pointvalue = $request->pointvalue;
            $chore->save();

            $assigner = auth()->user();
            $assignedTo = $request->assignedto;

            if (!empty($assignedTo)) {
                $userChore = new UserChores;
                $userChore->chore_id = $chore->id;
                $userChore->user_id = $assignedTo;
                $userChore->assigner_id = $assigner->id;
                $userChore->save();
            }
    
            $chores = chores::all()->load('user');
    
            return $chores;
        } else {

            return response('Forbidden', 404)->header('Content-Type', 'text/plain');
        }
    }

    /**
     * Display the specified resource.
     *
     * This is a details view. I'm not sure I need
     * this, but if so, I'd suspect it would need
     * to be admin only?
     * 
     * @param  \App\Models\chores  $chores
     * @return \Illuminate\Http\Response
     */
    // public function show(chores $chores, $id)
    // {

    //     $chore = $chores::where('id', $id)->first();

    //     return view('viewchore', ['chore' => $chore]);
    // }

    /**
     * Update the specified resource in storage.
     * 
     * Admin only
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\chores  $chores
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $choreId)
    {

        if (Gate::allows('manage-chorechart')) {

            $chore = chores::find($choreId)->load('user');


            $chore->chore = $request->chore;
            $chore->pointvalue = $request->pointvalue;
            $chore->save();

            // we have a user assigned to the chore
            if (isset($chore->user) && count($chore->user) > 0) {

                $userChore = UserChores::where([['chore_id', $chore->id], ['user_id', $chore->user[0]->id]])->first();
    
                $userChore->user_id = $request->assignedto;
                $userChore->save();
            } else {
                $userChore = new UserChores;
                $userChore->chore_id = $chore->id;
                $userChore->user_id = $request->assignedto;
                $userChore->save();
            }

            $choresList = chores::all()->load('user');
    
            return $choresList;
        } else {

            return response('Forbidden', 404)->header('Content-Type', 'text/plain');
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * Admin users only...
     *
     * @param  \App\Models\chores  $chores
     * @return \Illuminate\Http\Response
     */
    public function destroy(chores $chores, $id)
    {
        //
        if (Gate::allows('manage-chorechart')) {
            $chore = $chores->find($id);
            $chore->delete();

            // Hrm... I'm really not sure the on-delete cascade is working here
            // It might be something I need to get working.
            UserChores::where('chore_id', $id)->delete();

            $choresList = chores::all()->load('user');
    
            return $choresList;
        } else {

            return response('Forbidden', 404)->header('Content-Type', 'text/plain');
        }
    }
}