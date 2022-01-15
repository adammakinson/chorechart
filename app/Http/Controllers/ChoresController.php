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

class ChoresController extends Controller
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
    public function index(User $user, chores $chores)
    {
        if (Gate::allows('manage-chorechart')) {

            $chores = $chores->all();

            return $chores;

        } else {
            return response('Forbidden', 404)->header('Content-Type', 'text/plain');
        }
    }

    /**
     * Return a single chore by id
     */
    public function getChoreById(Request $request)
    {
        $chore = chores::find($request->choreId)->load('user');
        return $chore;
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

            return $chore;
        } else {

            return response('Forbidden', 403)->header('Content-Type', 'text/plain');
        }
    }

    /**
     * Update the specified resource in storage.
     * 
     * TODO: This thing is doing more than one thing. I need to extract it out
     * into the UserChoresController.
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

            $assigner = auth()->user();
            
            $chore = chores::find($choreId);


            $chore->chore = $request->chore;
            $chore->pointvalue = $request->pointvalue;
            $chore->save();


            $choresList = chores::all();


    
            return $choresList;
        } else {

            return response('Forbidden', 403)->header('Content-Type', 'text/plain');
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
        if (Gate::allows('manage-chorechart')) {
            $chore = $chores->find($id);
            
            

            $chore->delete();

            // Hrm... I'm really not sure the on-delete cascade is working here
            // It might be something I need to get working.
            UserChores::where('chore_id', $id)->delete();

            $choresList = chores::all();
    
            return $choresList;
        } else {

            return response('Forbidden', 403)->header('Content-Type', 'text/plain');
        }
    }
}