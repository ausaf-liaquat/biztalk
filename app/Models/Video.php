<?php

namespace App\Models;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id');
    }

    public function hashtags()
    {
        return $this->belongsToMany(Hashtag::class, 'hashtag_video', 'video_id', 'hashtag_id');
    }
}
