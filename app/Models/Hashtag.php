<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected $table = "hashtags";

    public function videos()
    {
        return $this->belongsToMany(Video::class, 'hashtag_video', 'hashtag_id', 'video_id');
    }
}
