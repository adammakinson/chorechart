<?php
/**
 * I'm wondering if all I need is one main controller
 * and certain 
 */
namespace App\Http\Controllers;

use App\Models\chores;
use App\Models\User;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ChoresApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * All users should be able to view basic chores
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        // $user = auth('api')->user();

        // dd($user);
        
        if (Gate::allows('view-chorechart')) {

            $chores = chores::all();
    
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
    
            return response('Chore created', 200)->header('Content-type', 'text/plain');
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
    public function update(Request $request, chores $chores)
    {
        //
        if (Gate::allows('manage-chorechart')) {

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

            $choresList = chores::all();
    
            return $choresList;
        } else {

            return response('Forbidden', 404)->header('Content-Type', 'text/plain');
        }
    }
}