<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reward AS RewardsModel;
use App\Models\Image AS ImageModel;
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
            
            $validatedData = $request->validate([
                'reward' => 'required',
                'pointvalue' => 'required',
                'file' => 'required'
            ]);
            
            $extension = $request->file->getClientOriginalExtension();
    
            $request->file->store('public/images');
    
            $reward = new RewardsModel;
            $reward->reward = $validatedData['reward'];
            $reward->point_value = $validatedData['pointvalue'];
            $reward->save();
    
            $image = new ImageModel;
            $image->reward_id = $reward->id;
            $image->path = '/images/';
            $image->filename = $request->file->hashName();
            $image->file_extension = $extension;
            $image->alt_text = "test image";
            $image->save();

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
