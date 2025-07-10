<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function get_goals()
    {
        // This method can be used to return a view if needed
        return response()->json(Goal::select('id', 'name')->get());
    }
}
