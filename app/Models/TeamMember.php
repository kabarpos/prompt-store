<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'bio',
        'image',
        'social_links',
        'order',
        'status'
    ];

    protected $casts = [
        'social_links' => 'array',
        'status' => 'string',
        'order' => 'integer'
    ];
}
