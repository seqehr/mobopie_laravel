<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowingPosts extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $table = 'followers';

    public function posts()
    {
        return $this->hasMany(Posts::class,  'user_id', 'follower_id');
    }
    public function stories()
    {
        return $this->hasMany(Stories::class,  'user_id', 'follower_id');
    }
    public function myposts()
    {
        return $this->hasMany(Posts::class,  'user_id', 'following_id');
    }
}
