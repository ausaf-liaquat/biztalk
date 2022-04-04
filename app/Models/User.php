<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'phone_no',
        'email',
        'password',
        'isaccount_public',
        'profile_image',
        'location',
        'country',
        'total_followings',
        'total_followers',
        'total_likes',
        'otp',
        'is_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime','ban_time'=>'datetime'
    ];

    public function generateOTP()
    {
        $this->timestamps = false;
        $this->otp = random_int(100000, 999999);
        // $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
    }
   
    public function resetOTP()
    {
        $this->timestamps = false;
        $this->otp = null;
       
        $this->save();
    }
   
}
