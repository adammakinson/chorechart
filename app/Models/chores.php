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

    public function user() {
        return $this->belongsToMany(User::class, 'user_chores', 'chore_id', 'user_id');
    }
}
