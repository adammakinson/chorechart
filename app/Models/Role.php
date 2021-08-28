<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * Populate timestamps
     */
    public $timestamps = true;

    protected $fillable = [
        'role'
    ];

    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}
