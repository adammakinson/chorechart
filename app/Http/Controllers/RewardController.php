<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reward AS RewardsModel;
use Illuminate\Support\Facades\Gate;

class RewardController extends Controller
{
    /**
     * Fetch all Rewards
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return RewardsModel::all()->load('images');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Gate::allows('manage-chorechart')) {

        } else {
            return response('Fobidden', 403)->header('Content-Type', 'application/json');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Gate::allows('manage-chorechart')) {

        } else {
            return response('Fobidden', 403)->header('Content-Type', 'application/json');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (Gate::allows('manage-chorechart')) {

        } else {
            return response('Fobidden', 403)->header('Content-Type', 'application/json');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::allows('manage-chorechart')) {

        } else {
            return response('Fobidden', 403)->header('Content-Type', 'application/json');
        }
    }
}
