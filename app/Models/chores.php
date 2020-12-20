<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class chores extends Model
{
    use HasFactory;
    use HasApiTokens;

    /**
     * Populate timestamps
     */
    public $timestamps = true;

    protected $fillable = [
        'chore',
        'pointvalue'
    ];
}
