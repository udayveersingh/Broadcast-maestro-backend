<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\MediaLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CampaignResource;
use App\Http\Requests\Campaign\CreateCampaignRequest;


class CampaignController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = Campaign::with('user');

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

        $campaigns = $query->latest()->paginate(10);

        return view('admin.campaigns.index', compact('campaigns'));
    }

    public function get_campaigns(Request $request)
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

        return response()->json($query->latest()->paginate(10));
    }

    public function edit($id)
    {
        $campaign = Campaign::with('user')->findOrFail($id);
        return view('admin.campaigns.edit', compact('campaign'));
    }

    public function update(Request $request, $id)
    {
         // Update base fields
        $campaign = Campaign::findOrFail($id);

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'budget' => 'required|numeric',
            'user_id' => 'nullable|exists:users,id',
            'goal_id' => 'nullable',
            'goal_id.*' => 'exists:goals,id',
            'target_audience_id' => 'nullable',
            'target_audience_id.*' => 'exists:target_audiences,id',
            'media_file' => 'nullable|file|mimes:jpg,jpeg,png,mp4,avi,pdf,docx|max:10240',
        ]);

        // Update fields
        $campaign->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'budget' => $request->budget,
            'user_id' => $request->user_id ?? auth()->id(),
            'status' => $request->status ?? 'draft',
        ]);

        // Sync goals (if any)
        if ($request->has('goal_id')) {
            $campaign->goals()->sync($request->goal_id);
        }

        // Sync target audiences (if any)
        if ($request->has('target_audience_id')) {
            $campaign->targetAudiences()->sync($request->target_audience_id);
        }

        // Handle new media files (append)
        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');

            // Delete old media relationships (but keep the actual media record if needed)
            $campaign->media()->detach();

            // OPTIONAL: Also delete media files and records from DB
            foreach ($campaign->media as $oldMedia) {
                Storage::disk('public')->delete($oldMedia->file_path);
                $oldMedia->delete(); // removes record from MediaLibrary
            }

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



        return response()->json([
            'message' => 'Campaign updated successfully.',
            'campaign' => new CampaignResource($campaign->load(['goals', 'targetAudiences', 'media']))
        ]);
        
    }

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

        return new CampaignResource($campaign->load(['goals', 'targetAudiences']));
    }

    public function destroy($id)
    {
        $campaign = Campaign::with(['goals', 'targetAudiences', 'media'])->findOrFail($id);

        // Detach related goals and target audiences
        $campaign->goals()->detach();
        $campaign->targetAudiences()->detach();

        // Delete media relationships and files
        foreach ($campaign->media as $media) {
            Storage::disk('public')->delete($media->file_path); // delete file
            $media->delete(); // delete media record if no longer needed elsewhere
        }

        // Detach media (if you prefer to keep media records, just do this)
        $campaign->media()->detach();

        // Soft delete the campaign
        $campaign->delete();

        return response()->json([
            'message' => 'Campaign deleted successfully (soft deleted).'
        ]);
    }


}
