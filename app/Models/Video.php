<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_flagged',
        'is_approved',
        'video_title',
        'video_description',
        'hashtags',
        'video_category',
        'investment_req',
        'allow_comment',
        'is_active',
        'video_name',
        'privacy',
        'location',
        'total_comments',
        'total_shares',
        'total_likes',
    ];

    public function users()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
