<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\CreateCampaignRequest;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use App\Models\CampaignTemplate;
use App\Models\MediaLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
    public function store(CreateCampaignRequest $request, $id = null)
    {
        $data = $request->only([
            'name',
            'description',
            'type',
            'start_date',
            'end_date',
            'budget',
            'status'
        ]);
        $data['user_id'] = auth()->id();

        if ($id) {
            $campaign = Campaign::findOrFail($id);
            $campaign->update($data);
        } else {
            $campaign = Campaign::create($data);
        }

        // Sync (not attach) relationships for updates
        if ($request->has('goal_id')) {
            $campaign->goals()->sync($request->goal_id); // use sync instead of attach
        }

        if ($request->has('target_audience_id')) {
            $campaign->targetAudiences()->sync($request->target_audience_id);
        }

        // Handle multiple media files
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

        // Handle single media file
        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');

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


    // public function store(CreateCampaignRequest $request)
    // {
    //     // Set CORS headers function
    //     // $setCorsHeaders = function($response) {
    //     //     $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5173');
    //     //     $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    //     //     $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    //     //     return $response;
    //     // };

    //     try {
    //         $data = $request->only([
    //             'name', 'description', 'type',
    //             'start_date', 'end_date', 'budget', 'status'
    //         ]);
    //         $data['user_id'] = auth()->id();

    //         $campaign = Campaign::create($data);

    //         // Rest of your code...
    //         if ($request->has('goal_id')) {
    //             $campaign->goals()->attach($request->goal_id);
    //         }

    //         if ($request->has('target_audience_id')) {
    //             $campaign->targetAudiences()->attach($request->target_audience_id);
    //         }

    //         if ($request->hasFile('media_files')) {
    //             foreach ($request->file('media_files') as $file) {
    //                 $path = $file->store('uploads/media', 'public');

    //                 $media = MediaLibrary::create([
    //                     'user_id' => auth()->id(),
    //                     'filename' => basename($path),
    //                     'original_name' => $file->getClientOriginalName(),
    //                     'file_path' => $path,
    //                     'file_size' => $file->getSize(),
    //                     'mime_type' => $file->getMimeType(),
    //                 ]);

    //                 $campaign->media()->attach($media->id);
    //             }
    //         }

    //         if ($request->hasFile('media_file')) {
    //             $file = $request->file('media_file');
    //             $path = $file->store('uploads/media', 'public');

    //             $media = MediaLibrary::create([
    //                 'user_id' => auth()->id(),
    //                 'filename' => basename($path),
    //                 'original_name' => $file->getClientOriginalName(),
    //                 'file_path' => $path,
    //                 'file_size' => $file->getSize(),
    //                 'mime_type' => $file->getMimeType(),
    //             ]);

    //             $campaign->media()->attach($media->id);
    //         }

    //         $response = response()->json(new CampaignResource($campaign->load(['goals', 'targetAudiences', 'media'])));
    //         // return $setCorsHeaders($response);
    //         return $response;


    //     } catch (\Exception $e) {
    //         // Log the actual error
    //         \Log::error('Campaign creation error: ' . $e->getMessage());

    //         $response = response()->json([
    //             'error' => 'Campaign creation failed',
    //             'message' => $e->getMessage()
    //         ], 500);

    //         // return $setCorsHeaders($response);
    //         return $response;
    //     }
    // }

    public function index(Request $request)
    {
        $query = Campaign::with(['user', 'media', 'targetAudiences', 'goals']);

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

    public function myCampaigns(Request $request)
    {
        $user = $request->user();

        $query = Campaign::with(['user', 'media', 'targetAudiences', 'goals'])
            ->where('user_id', $user->id);

        // Add filters if needed, same as before
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

    public function getCompaign($id)
    {
        $userID = auth()->id();

        if (is_null($userID)) {
            return response()->json(['success' => false, 'message' => "Unauthorized"], 401);
        }

        $campaignData = Campaign::with('targetAudiences', 'goals','media')->find($id);
        dd(optional($campaignData->media->first())->id);
        $CampaignTemplates = CampaignTemplate::where('campaign_id','=',$id)->get(); 

        $campaign = [
            'id' => $campaignData->id,
            'user_id' => $campaignData->user_id,
            'name' => $campaignData->name,
            'description' => $campaignData->description,
            'type' => $campaignData->type,
            'status' => $campaignData->status,
            'start_date' =>  $campaignData->start_date,
            'end_date' =>  $campaignData->end_date,
            'budget' =>  $campaignData->budget,
            'goal_id' => optional($campaignData->goals->first())->id,
            'target_audience_id' => optional($campaignData->targetAudiences->first())->id,
            ''
        ];

        if (!$campaign) {
            return response()->json(['success' => false, 'message' => 'compaign not found.'], 404);
        } else {
            return response()->json([
                'success' => true,
                'compaign' =>  $campaign,
                'CampaignTemplates' =>  $CampaignTemplates
            ], 200);
        }
    }

    public function destroy($id)
    {
        $userID = auth()->id();

        if (is_null($userID)) {
            return response()->json(['success' => false, 'message' => "Unauthorized"], 401);
        }

        $campaign = Campaign::find($id);

        if (!$campaign) {
            return response()->json(['success' => false, 'message' => 'Campaign not found.'], 404);
        }

        $campaign->delete();

        return response()->json(['success' => true, 'message' => 'Campaign deleted successfully.']);
    }


    public function getCampaignTemplate()
    {
        $userID = auth()->id();
        if (is_null($userID)) {
            return response()->json(['success' => false, 'message' => "Unauthorized"], 401);
        }

        $campaign_template =  CampaignTemplate::where('user_id', '=', $userID)->get();
        if (!$campaign_template) {
            return response()->json(['success' => false, 'message' => 'Campaign template not found.'], 404);
        } else {
            return response()->json([
                'success' => true,
                'campaign_templates' => $campaign_template
            ], 200);
        }
    }


    public function storeCampTemp(Request $request)
    {
        $userID = auth()->id();
        if (is_null($userID)) {
            return response()->json(['success' => false, 'message' => "Unauthorized"], 401);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'campaign_id' => 'required',
            'tool_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
                'success' => false,
                'message' =>  $error
            ], 400);
        }

        $user_campaign_template = CampaignTemplate::where('user_id', '=', $userID)->where('tool_id', '=', $request->input('tool_id'))->where('campaign_id', '=', $request->input('campaign_id'))->first();

        if (!empty($user_campaign_template)) {
            $campaign_template = CampaignTemplate::find($user_campaign_template->id);
            $message = 'Campaign template already assigned to the user. Campaign template updated.';
        } else {
            $campaign_template = new CampaignTemplate();
            $message = 'Campaign template has been created successfully';
        }
        $campaign_template->user_id =  $userID;
        $campaign_template->campaign_id = $request->input('campaign_id');
        $campaign_template->tool_id = $request->input('tool_id');
        $campaign_template->title = $request->input('title');
        $campaign_template->description = $request->input('description');
        $campaign_template->save();

        return response()->json(['success' => true, 'message' =>  $message, 'data' => $campaign_template], 200);
    }
}
