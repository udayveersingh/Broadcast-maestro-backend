<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdminUserTool;
use App\Models\TargetAudience;
use Illuminate\Http\Request;
use App\Models\Tool;
use Illuminate\Support\Facades\Validator;

class ToolsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tools",
     *     tags={"Tools"},
     *     summary="List available tools",
     *     description="Returns a list of tools, optionally filtered by goal name.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="goal",
     *         in="query",
     *         required=false,
     *         description="Filter tools by associated goal name",
     *         @OA\Schema(type="string", example="Increase Website Traffic")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of tools",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Google Ads"),
     *                 @OA\Property(property="description", type="string", example="Advertising tool for online visibility."),
     *                 @OA\Property(
     *                     property="goals",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Increase Website Traffic")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $goal = $request->query('goal');

        $tools = Tool::with('goals')
            ->when($goal, function ($query) use ($goal) {
                $query->whereHas('goals', function ($q) use ($goal) {
                    $q->where('name', $goal);
                });
            })->paginate(5);

        return response()->json($tools);
    }


    public function getUserTools(Request $request)
    {
        $userID = auth()->id();

        if (is_null($userID)) {
            return response()->json(['success' => false, 'message' => "Invalid Request"], 401);
        } else {
            $user_tools = AdminUserTool::where('user_id', '=', $userID)
                ->select('tool_id', 'target_audience', 'name', 'content_prompt', 'budget', 'deadline', 'supplier', 'goals')
                ->latest()
                ->get()
                ->map(function ($tool) {
                    // Convert JSON string fields to arrays
                    $tool->target_audience = json_decode($tool->target_audience, true);
                    $tool->goals = json_decode($tool->goals, true);
                    return $tool;
                });

            $admin_tools = Tool::select('id', 'name', 'content_prompt', 'budget', 'deadline', 'supplier')->latest()->get();
            return response()->json(['success' => true, 'userTools' =>  $user_tools, 'adminTools' => $admin_tools], 200);
        }
    }

    public function assignUserTools(Request $request, $id)
    {
        $user_id = $id;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'tool_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
                'success' => false,
                'message' =>  $error
            ], 400);
        }


        $goals = $request->input('goals');
        $goalValues = $request->input('goals_value');

        $finalGoals = array_map(function ($id, $val) {
            return ['goal_id' => $id, 'value' => $val];
        }, $goals, $goalValues);

        $goals_json_format = json_encode($finalGoals);
 
        $target_audiences = $request->input('target_audience');
 
        $admin_user_tools = AdminUserTool::where('user_id', '=', $id)->where('tool_id', '=', $request->input('tool_id'))->first();

        if (!empty($admin_user_tools)) {
            $user_tools = AdminUserTool::find($admin_user_tools->id);
            $message = 'Tool already assigned to the user. Assignment updated.';
        } else {
            $user_tools = new AdminUserTool();
            $message = 'Tool has been successfully assigned to the user.';
        }
        $user_tools->user_id =  $user_id;
        $user_tools->tool_id = $request->input('tool_id');
        $user_tools->name = $request->input('name');
        $user_tools->content_prompt = $request->input('content_prompt');
        $user_tools->budget = $request->input('budget');
        $user_tools->deadline = $request->input('deadline');
        $user_tools->supplier = $request->input('supplier');
        $user_tools->target_audience = implode(',',$target_audiences);
        $user_tools->goals =  $goals_json_format;
        $user_tools->save();
        return response()->json(['success' => true, 'message' =>  $message, 'data' => $user_tools], 200);
    }



    public function getTargetAudience()
    {
        $userID = auth()->id();

        if (is_null($userID)) {
            return response()->json(['success' => false, 'message' => "Invalid Request"], 401);
        } else {
            $target_audiences =  TargetAudience::select('id', 'name')->orderBy('id', 'DESC')->get();

            return response()->json(['success' => true, 'targetAudiences' => $target_audiences], 200);
        }
    }

    public function destroy($id)
    {
        $userID = auth()->id();
        if (is_null($userID)) {
            return response()->json(['success' => false, 'message' => "Unauthorized"], 401);
        }

        $admin_user_tools = AdminUserTool::where('tool_id', $id)->where('user_id', $userID)->first();

        if (!$admin_user_tools) {
            return response()->json(['success' => false, 'message' => 'User tools not found.'], 404);
        }

        $admin_user_tools->delete();

        return response()->json(['success' => true, 'message' => 'User tools deleted successfully.']);
    }
}
