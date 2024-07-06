<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Models\Image AS ImageModel;
use App\Models\Reward AS RewardsModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

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
                'file' => '30_game_time.svg',
                'imgalt' => 'Roblox time'
            ],
            [
                'reward' => 'One 30 minute episode',
                'point_value' => 30,
                'file' => 'tv_time.svg',
                'imgalt' => 'One TV Episode'
            ]
        ];
        
        foreach ($seedData as $seed) {
            
            $rewardInstance = new RewardsModel([
                'reward' => $seed['reward'],
                'point_value' => $seed['point_value']
            ]);

            $rewardInstance->save();

            // Get the path to the file(s) that the seeder needs
            $seederImageFileLocation = Storage::path('seederfiles/' . $seed['file']);
            
            // Create a new file with the retrieved path
            $seederImageFile = new File($seederImageFileLocation);
            
            // Generate the hash name same as how it would be generated on upload
            $seederImageFileHashName = $seederImageFile->hashName();

            // copy the file over from the seederfiles directory as the hash name
            $seederImage = Storage::copy('seederfiles/'.$seed['file'], 'public/images/' . $seederImageFileHashName);

            // define the storage location for the database entry
            $storageLocation = env('FILESYSTEM_DRIVER', '') . '/images/';

            // define the extension for the database entry
            $extension = $seederImageFile->extension();

            // generate the database record
            $rewardImageInstance = new ImageModel;
            $rewardImageInstance->reward_id = $rewardInstance->id;
            $rewardImageInstance->path = $storageLocation;
            $rewardImageInstance->filename = $seederImageFile->hashName();
            $rewardImageInstance->file_extension = $extension;
            $rewardImageInstance->alt_text = $seed['imgalt'];
            $rewardImageInstance->save();
        }
    }
}
