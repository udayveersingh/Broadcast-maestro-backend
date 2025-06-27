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
