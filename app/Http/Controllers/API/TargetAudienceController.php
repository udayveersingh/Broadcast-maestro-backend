<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TargetAudience;
use Illuminate\Http\Request;

class TargetAudienceController extends Controller
{
    public function index()
    {
        return response()->json(
            TargetAudience::select('id', 'name')
                ->where('user_id', auth()->id()) // Optional: filter by user
                ->get()
        );
    }
}
