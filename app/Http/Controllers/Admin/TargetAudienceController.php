<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TargetAudience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TargetAudienceController extends Controller
{
    public function index()
    {
        return response()->json(
            TargetAudience::select('id', 'name')
                ->get()
        );
    }


    public function data()
    {
        return response()->json(TargetAudience::latest()->get());
    }

    public function list()
    {
        return view('admin.target-audience.index');
    }

    public function create($id = "")
    {
        if (!empty($id)) {
            $data['target_audience'] = TargetAudience::find($id);
            return view('admin.target-audience.create', $data);
        } else {
            return view('admin.target-audience.create');
        }
    }


    public function store(Request $request, $id = "")
    {

        $userId =  Auth::user()->id;
        if (!empty($id)) {
            $TargetAudience = TargetAudience::find($id);
            $message = 'Target Audience updated successfully.';
        } else {
            $TargetAudience = new TargetAudience();
            $message = 'Target Audience created successfully.';
        }
        $TargetAudience->user_id = $userId;
        $TargetAudience->name = $request->input('target_audience');
        $TargetAudience->description = $request->input('target_audience') . ' group';
        $TargetAudience->criteria = json_encode([]);
        $TargetAudience->save();
        return redirect()->route('admin.target-audiences.list')->with('success', $message);
    }


    public function destroy($id)
    {
        $TargetAudience = TargetAudience::find($id);
        $TargetAudience->delete();
        return response()->json(['success' => true]);
    }


    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        // Ensure that the ids are an array of integers
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:target_audiences,id',
        ]);

        TargetAudience::destroy($ids);  // Delete the records by IDs

        return response()->json(['success' => true]);
    }
}
