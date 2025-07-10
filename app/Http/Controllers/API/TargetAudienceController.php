<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TargetAudience;
use Illuminate\Http\Request;

class TargetAudienceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/target-audiences",
     *     tags={"Target Audiences"},
     *     summary="List target audiences",
     *     description="Returns a list of all target audiences (id and name).",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of target audiences",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Young Professionals")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index()
    {
        return response()->json(
            TargetAudience::select('id', 'name')
                ->get()
        );
    }
}
