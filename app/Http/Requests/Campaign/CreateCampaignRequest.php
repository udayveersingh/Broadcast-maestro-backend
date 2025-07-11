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
        // return [
        //     'name' => 'required|string|max:255',
        //     'description' => 'nullable|string',
        //     'type' => 'required|in:email,social,sms,push',
        //     'start_date' => 'nullable|date',
        //     'end_date' => 'nullable|date|after_or_equal:start_date',
        //     'budget' => 'nullable|numeric',
        //     'goal_ids' => 'array',
        //     'goal_ids.*' => 'exists:goals,id',
        //     'target_audience_ids' => 'array',
        //     'target_audience_ids.*' => 'exists:target_audiences,id',

        //     'media_files' => 'nullable|array',
        //     'media_files.*' => 'file|mimes:jpg,jpeg,png,webp,mp4|max:10240',
        // ];
        return [
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
        ];

    }
}
