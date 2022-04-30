<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'message', 'status', 'title'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'contacts';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
