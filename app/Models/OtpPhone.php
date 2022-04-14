<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpPhone extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'mac_id',
        'phone',
    ];

    protected $casts = [
        'send_date',
    ];
}
