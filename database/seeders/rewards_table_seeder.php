<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Models\Image AS ImageModel;
use App\Models\Reward AS RewardsModel;

class rewards_table_seeder extends Seeder
{
    /**
     * TODO - There is a problem with this seeder. It doesn't
     * take into account the images attached to the rewards...
     */
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seedData = [
            [
                'reward' => '30 minutes of Roblox time',
                'point_value' => 30,
                'file' => 'robloxtimetest.jpg',
                'imgalt' => 'Roblox time'
            ],
            [
                'reward' => 'One 30 minute episode',
                'point_value' => 30,
                'file' => 'episodetimetest.jpg',
                'imgalt' => 'One TV Episode'
            ]
        ];
        
        foreach ($seedData as $seed) {
            
            $rewardInstance = new RewardsModel([
                'reward' => $seed['reward'],
                'point_value' => $seed['point_value']
            ]);

            $rewardInstance->save();

            $imageSeed = UploadedFile::fake()->image($seed['file']);
            $storageLocation = env('FILESYSTEM_DRIVER', 'public') . '/images';

            $imageSeed->store($storageLocation);
            
            $extension = $imageSeed->getClientOriginalExtension();

            $rewardImageInstance = new ImageModel;
            $rewardImageInstance->reward_id = $rewardInstance->id;
            $rewardImageInstance->path = $storageLocation;
            $rewardImageInstance->filename = $imageSeed->hashName();
            $rewardImageInstance->file_extension = $extension;
            $rewardImageInstance->alt_text = $seed['imgalt'];
            $rewardImageInstance->save();
        }
    }
}
