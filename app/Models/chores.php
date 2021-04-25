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

    public function user() {
        if (Gate::allows('manage-chorechart')) {
            return $this->belongsToMany(User::class, 'user_chores', 'chore_id', 'user_id')->withPivot('inspection_ready', 'inspected_on', 'pending', 'inspection_passed', 'points_awarded');
        } else {
            return $this->belongsToMany(User::class, 'user_chores', 'chore_id', 'user_id')->withPivot('inspection_ready', 'pending', 'inspection_passed', 'points_awarded');
        }
        
        // if (Gate::allows('manage-chorechart')) {
        //     return $this->belongsToMany(User::class, 'user_chores', 'chore_id', 'user_id')->using(UserChores::class);
        // } else {
        //     return $this->belongsToMany(User::class, 'user_chores', 'chore_id', 'user_id')->using(UserChores::class);
        // }
    }

    public function assigner() {
        return $this->belongsToMany(User::class, 'user_chores', 'chore_id', 'assigner_id');
    }
}
