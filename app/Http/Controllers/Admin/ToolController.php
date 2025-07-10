<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.tools.index');
    }

    public function get_tools(Request $request)
    {
        // Fetch tools from the database, possibly with pagination
        // $tools = Tool::paginate(10);
        $goal = $request->query('goal');

        $tools = Tool::with('goals')
            ->when($goal, function ($query) use ($goal) {
                $query->whereHas('goals', function ($q) use ($goal) {
                    $q->where('name', $goal);
                });
            })->paginate(20);

        return response()->json([
            'tools' => $tools
        ]);
    }

    public function update(Request $request, Tool $tool)
    {
        $tool->update($request->only([
            'content_prompt', 'budget', 'deadline', 'supplier'
        ]));

        if ($request->has('goal_ids')) {
            $tool->goals()->sync($request->goal_ids);
        }

        return response()->json($tool->load('goals'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'content_prompt' => 'nullable|string',
            'budget' => 'nullable|integer',
            'deadline' => 'nullable|integer',
            'supplier' => 'nullable|string',
            'goal_ids' => 'array', // e.g. [1, 2]
            'goal_ids.*' => 'exists:goals,id'
        ]);

        $tool = Tool::create($data);
        $tool->goals()->sync($data['goal_ids'] ?? []);

        return response()->json($tool->load('goals'), 201);
    }

    public function destroy(Tool $tool)
    {
        try {
            $tool->goals()->detach(); // Detach from pivot table first
            $tool->delete();

            return response()->json(['message' => 'Tool deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete tool.'], 500);
        }
    }
}
