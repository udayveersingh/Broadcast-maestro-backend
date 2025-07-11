<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TargetAudience;
use Illuminate\Http\Request;

class TargetAudienceController extends Controller
{
    public function index()
    {
        return response()->json(
            TargetAudience::select('id', 'name')
                ->get()
        );
    }
}
