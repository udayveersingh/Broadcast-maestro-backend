<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'organization',
        'department',
        'country',
        'state',
        'city',
        'address',
        'zip_code',
        'photo_visibility',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
