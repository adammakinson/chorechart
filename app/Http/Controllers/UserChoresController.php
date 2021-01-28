<?php

namespace App\Http\Controllers;

use App\Models\UserChores;
use Illuminate\Http\Request;

class UserChoresController extends Controller
{
    // public function index()
    // {
    //     $allUserChores = UserChores::all();

    //     return $allUserChores;
    // }
    
    public function update(Request $request)
    {
        echo "updating inspection ready";
    }
}
