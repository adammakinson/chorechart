<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Gate;

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
        if (Gate::allows('manage-chorechart')) {
            return $this->belongsToMany(User::class, 'user_chores', 'chore_id', 'user_id')->withPivot('inspection_ready', 'inspected_on', 'inspection_passed');
        } else {
            return $this->belongsToMany(User::class, 'user_chores', 'chore_id', 'user_id')->withPivot('inspection_ready');
        }
    }

    public function assigner() {
        return $this->belongsToMany(User::class, 'user_chores', 'chore_id', 'assigner_id');
    }
}
