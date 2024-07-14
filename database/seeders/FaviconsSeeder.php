<?php
namespace Database\Seeders;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Database\Seeder;

class FaviconsSeeder extends seeder {

    public function run() {
        
        $favicons = [
            'android-chrome-192x192.png',
            'android-chrome-512x512.png',
            'apple-touch-icon.png',
            'favicon-16x16.png',
            'favicon-32x32.png',
            'favicon.ico',
            'site.webmanifest'
        ];

        foreach ($favicons as $fileName) {
            Storage::copy('seederfiles/'.$fileName, 'public/images/' . $fileName);
        }
    }
}