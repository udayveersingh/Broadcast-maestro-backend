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
     *     description="Creates a campaign with optional goal, target audience, and media files (single or multiple).",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "type", "start_date", "end_date", "budget", "status"},
     *                 @OA\Property(property="name", type="string", example="New Year Campaign"),
     *                 @OA\Property(property="description", type="string", example="Promote New Year offers"),
     *                 @OA\Property(property="type", type="string", example="Social Media"),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2025-12-25"),
     *                 @OA\Property(property="end_date", type="string", format="date", example="2026-01-10"),
     *                 @OA\Property(property="budget", type="number", format="float", example=10000),
     *                 @OA\Property(property="status", type="string", example="draft"),
     *                 @OA\Property(property="goal_id", type="integer", example=2),
     *                 @OA\Property(property="target_audience_id", type="integer", example=5),
     *                 @OA\Property(
     *                     property="media_file",
     *                     type="string",
     *                     format="binary",
     *                     description="Single media file upload"
     *                 ),
     *                 @OA\Property(
     *                     property="media_files",
     *                     type="array",
     *                     @OA\Items(type="string", format="binary"),
     *                     description="Multiple media file uploads"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Campaign created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object", description="Campaign resource with goals, target audiences, and media")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
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
        'start_date', 'end_date', 'budget','status'
        ]);
        $data['user_id'] = auth()->id();

        $campaign = Campaign::create($data);

        // Attach relationships
        if ($request->has('goal_id')) {
            $campaign->goals()->attach($request->goal_id);
        }

        if ($request->has('target_audience_id')) {
            $campaign->targetAudiences()->attach($request->target_audience_id);
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

         if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');

            // Upload new file
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

        return new CampaignResource($campaign->load(['goals', 'targetAudiences', 'media']));
    }

    public function index(Request $request)
    {
         $query = Campaign::with(['user', 'media','targetAudiences', 'goals']); 

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%');
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        $perPage = $request->get('per_page', 10);

        return response()->json($query->latest()->paginate($perPage));
    }
}
