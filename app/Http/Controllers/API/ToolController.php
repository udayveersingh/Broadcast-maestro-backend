<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tool;

class ToolController extends Controller
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
            })->get();

        return response()->json($tools);
    }

}
