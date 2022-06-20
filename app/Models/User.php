<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $appends = ['avatar'];
    protected $fillable = [
        'name',
        'email',
        'password',
        'img',
        'status',
        'fcm_token',
        'bg',
        'bio',
        'fname',
        'lname',
        'region',
        'gender',
        'title',
        'birthday',
        'height',
        'language',
        'relationship',
        'sex',
        'religion',
        'personality',
        'education',
        'work'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function followers()
    {
        return $this->hasMany(Followers::class, 'follower_id');
    }

    public function posts()
    {
        return $this->hasMany(Posts::class, 'user_id');
    }
    public function getAvatarAttribute()
    {
        return env('DEFAULT_URL')   . $this->attributes['img'];
    }
}
