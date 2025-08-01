<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdminUserTool;
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
            $user_tools =  AdminUserTool::where('user_id','=',$userID)->select('name','budget','deadline','supplier')->latest()->get();
            $admin_tools = Tool::select('name','budget','deadline','supplier')->latest()->get();
            return response()->json(['success' => true, 'userTools' =>  $user_tools,'adminTools'=>$admin_tools], 200);
        }
    }

    public function assignUserTools(Request $request, $id)
    {
        $user_id = $id;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
                'success' => false,
                'message' =>  $error
            ], 400);
        }

        $admin_user_tools = new AdminUserTool();
        $admin_user_tools->user_id =  $user_id;
        $admin_user_tools->tool_id = $request->input('tool_id');
        $admin_user_tools->name = $request->input('name');
        $admin_user_tools->content_prompt = $request->input('content_prompt');
        $admin_user_tools->budget = $request->input('budget');
        $admin_user_tools->deadline = $request->input('deadline');
        $admin_user_tools->supplier = $request->input('supplier');
        $admin_user_tools->target_audience = $request->input('target_audience');
        $admin_user_tools->save();
        return response()->json(['success' => true, 'message' => 'tool assign to user successfull.', 'data' => $admin_user_tools], 200);
    }
}
