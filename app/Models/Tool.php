<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    protected $fillable = [
        'name', 'content_prompt', 'budget', 'deadline', 'goal', 'supplier'
    ];

    public function goals()
    {
        return $this->belongsToMany(Goal::class);
    }

}

