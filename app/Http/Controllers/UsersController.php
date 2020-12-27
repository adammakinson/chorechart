<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * All users should be able to view basic chores
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::allows('manage-chorechart')) {
            $users = User::all();

            return $users;
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

        } else {
            return response('Forbidden', 404)->header('Content-Type', 'text/plain');
        }
    }

    /**
     * Update the specified resource in storage.
     * 
     * Admin only
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\chores  $chores
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $userId)
    {
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
    public function destroy(User $user, $id)
    {
        if (Gate::allows('manage-chorechart')) {

        } else {
            return response('Forbidden', 404)->header('Content-Type', 'text/plain');
        }
    }
}
