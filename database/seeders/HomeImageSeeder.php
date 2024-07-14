<?php
namespace Database\Seeders;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Database\Seeder;

class HomeImageSeeder extends seeder {

    public function run() {
        $fileName = 'sponge.svg';

        // copy the file over from the seederfiles directory as the hash name
        $seederImage = Storage::copy('seederfiles/'.$fileName, 'public/images/' . $fileName);
    }

}