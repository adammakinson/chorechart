<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserChores extends Pivot
{
    use HasFactory;

    protected $table = 'user_chores';

    protected $fillable = [
        'chore_id',
        'user_id',
        'assigner_id',
        'inspection_ready',
        'inspected_on',
        'inspection_passed',
        'pending'
    ];
}
