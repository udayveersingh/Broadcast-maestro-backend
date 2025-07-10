<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolParameter extends Model
{
    protected $fillable = ['tool_id', 'name', 'type', 'validation', 'is_required'];

    protected $casts = [
        'validation' => 'array',
        'is_required' => 'boolean',
    ];

    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }
}

