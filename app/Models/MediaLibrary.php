<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaLibrary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'filename',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
        'alt_text',
    ];

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_media', 'media_library_id', 'campaign_id');
    }


}
