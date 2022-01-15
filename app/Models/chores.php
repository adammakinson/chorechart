<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Gate;
use App\Models\UserChores;
use Illuminate\Database\Eloquent\SoftDeletes;

class chores extends Model
{
    use HasFactory;
    use HasApiTokens;
    use SoftDeletes;

    /**
     * Populate timestamps
     */
    public $timestamps = true;

    protected $fillable = [
        'chore',
        'pointvalue'
    ];

}
