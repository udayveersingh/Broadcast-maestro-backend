<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'email'];

    public function tools()
    {
        return $this->hasMany(Tool::class);
    }
}

