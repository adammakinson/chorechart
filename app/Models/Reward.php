<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Image;

class Reward extends Model
{
    use HasFactory;

    // reward images relationship
    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
