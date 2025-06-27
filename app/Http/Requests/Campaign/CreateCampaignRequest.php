<?php

namespace App\Http\Requests\Campaign;

use Illuminate\Foundation\Http\FormRequest;

class CreateCampaignRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Sanctum already handles auth
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:email,social,sms,push',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric',
            'goal_ids' => 'array',
            'goal_ids.*' => 'exists:goals,id',
            'target_audience_ids' => 'array',
            'target_audience_ids.*' => 'exists:target_audiences,id',

            'media_files' => 'nullable|array',
            'media_files.*' => 'file|mimes:jpg,jpeg,png,webp,mp4|max:10240',
        ];

    }
}
