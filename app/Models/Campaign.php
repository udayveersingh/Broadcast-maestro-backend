<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'description', 'type', 'status',
        'start_date', 'end_date', 'budget', 'target_audience_id'
    ];

    public function goals()
    {
        return $this->belongsToMany(Goal::class, 'campaign_goal');
    }

    public function targetAudiences()
    {
        return $this->belongsToMany(TargetAudience::class, 'campaign_target_audience');
    }

    public function media()
    {
        return $this->belongsToMany(MediaLibrary::class, 'campaign_media');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    





}
