<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'price_range',
    ];
    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
