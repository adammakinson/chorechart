<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reward AS RewardsModel;
use App\Models\Image AS ImageModel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

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
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $rewardId)
    {
        if (Gate::allows('manage-chorechart')) {

            $validatedData = $request->validate([
                'reward' => 'required',
                'pointvalue' => 'required'
            ]);
            
            $reward = RewardsModel::find($rewardId);
            $reward->reward = $validatedData['reward'];
            $reward->point_value = $validatedData['pointvalue'];
            $reward->save();
 
            if($request->hasFile('file') && $request->file('file')->isValid()) {
                
                $storedImage = ImageModel::where('reward_id', $rewardId)->first();

                if ($storedImage) {
                    Storage::delete($storedImage->filename);
                    
                    $extension = $request->file->getClientOriginalExtension();

                    $storageLocation = env('FILESYSTEM_DRIVER', 'public') . '/images';

                    $request->file->store($storageLocation);
    
                    $storedImage->filename = $request->file->hashName();
                    $storedImage->file_extension = $extension;
                    $storedImage->save();
                } else {
                    $storageLocation = env('FILESYSTEM_DRIVER', 'public') . '/images';

                    $request->file->store($storageLocation);
                    
                    $extension = $request->file->getClientOriginalExtension();

                    $image = new ImageModel;
                    $image->reward_id = $reward->id;
                    $image->path = '/testImages/';
                    $image->filename = $request->file->hashName();
                    $image->file_extension = $extension;
                    $image->alt_text = "test image";
                    $image->save();
                }

                return response('OK', 200)->header('Content-Type', 'application/json');
            }
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
    public function destroy(RewardsModel $reward)
    {
        if (Gate::allows('manage-chorechart')) {
            $reward->delete();
        } else {
            return response('Fobidden', 403)->header('Content-Type', 'application/json');
        }
    }
}
