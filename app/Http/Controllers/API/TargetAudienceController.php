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
        $userID = auth()->id();

        if (is_null($userID)) {
            return response()->json(['success' => false, 'message' => "Invalid Request"], 401);
        } else {
            $target_audiences =  TargetAudience::select('id', 'name')->orderBy('id', 'DESC')->get();

            return response()->json(['success' => true, 'targetAudiences' => $target_audiences], 200);
        }
    }


    public function store(Request $request, $id = null)
    {
        $userID = auth()->id();

        if (is_null($userID)) {
            return response()->json(['success' => false, 'message' => "Invalid Request"], 401);
        }

        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        if (!empty($id)) {
            $TargetAudience = TargetAudience::findOrFail($id); // Use findOrFail for safety
            $message = 'Target Audience updated successfully.';
        } else {
            $TargetAudience = new TargetAudience();
            $message = 'Target Audience created successfully.';
        }

        $TargetAudience->user_id = $userID;
        $TargetAudience->name = $request->input('name');
        $TargetAudience->description = $request->input('name') . ' group';
        $TargetAudience->criteria = json_encode([]);
        $TargetAudience->save();

        return response()->json([
            'success' => true,
            'message' => $message,
            'targetAudiences' => $TargetAudience
        ], 200);
    }


    public function destroy($id)
    {
        $userID = auth()->id();

        if (is_null($userID)) {
            return response()->json(['success' => false, 'message' => "Unauthorized"], 401);
        }

        $targetAudience = TargetAudience::where('id', $id)->where('user_id', $userID)->first();

        if (!$targetAudience) {
            return response()->json(['success' => false, 'message' => 'Target Audience not found.'], 404);
        }

        $targetAudience->delete();

        return response()->json(['success' => true, 'message' => 'Target Audience deleted successfully.']);
    }
}
