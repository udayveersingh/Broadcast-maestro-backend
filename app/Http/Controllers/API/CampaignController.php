<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\CreateCampaignRequest;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use App\Models\MediaLibrary;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/campaigns",
     *     tags={"Campaigns"},
     *     summary="Create a new campaign",
     *     description="Creates a campaign with related goals, target audiences, and media file uploads.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "type", "start_date", "end_date", "budget"},
     *                 @OA\Property(property="name", type="string", example="Diwali Campaign"),
     *                 @OA\Property(property="description", type="string", example="Promote Diwali offers"),
     *                 @OA\Property(property="type", type="string", example="Email"),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2025-10-01"),
     *                 @OA\Property(property="end_date", type="string", format="date", example="2025-10-31"),
     *                 @OA\Property(property="budget", type="number", format="float", example=5000),
     *                 @OA\Property(
     *                     property="goal_ids",
     *                     type="array",
     *                     @OA\Items(type="integer", example=1)
     *                 ),
     *                 @OA\Property(
     *                     property="target_audience_ids",
     *                     type="array",
     *                     @OA\Items(type="integer", example=2)
     *                 ),
     *                 @OA\Property(
     *                     property="media_files",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Campaign created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object", description="Created campaign resource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
     public function store(CreateCampaignRequest $request)
    {
        $data = $request->only([
        'name', 'description', 'type',
        'start_date', 'end_date', 'budget'
        ]);
        $data['user_id'] = auth()->id();

        $campaign = Campaign::create($data);

        // Attach relationships
        if ($request->has('goal_ids')) {
            $campaign->goals()->attach($request->goal_ids);
        }

        if ($request->has('target_audience_ids')) {
            $campaign->targetAudiences()->attach($request->target_audience_ids);
        }

        if ($request->hasFile('media_files')) {
            foreach ($request->file('media_files') as $file) {
                $path = $file->store('uploads/media', 'public');

                $media = MediaLibrary::create([
                    'user_id' => auth()->id(),
                    'filename' => basename($path),
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);

                $campaign->media()->attach($media->id);
            }
        }

        return new CampaignResource($campaign->load(['goals', 'targetAudiences']));
    }
}
