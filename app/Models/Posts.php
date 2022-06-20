<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Posts extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'title',
        'user_id',
        'inputs',
        'category',
        'caption',
        'lat',
        'lon',
        'loc',

    ];

    // protected $hidden = ['input1', 'input2', 'input3', 'input4', 'input5'];
    public function comments()
    {
        return $this->hasMany(PostComments::class, 'post_id')->whereNull('parent_id');
    }

    public function likes()
    {
        return $this->hasMany(PostLikes::class, 'post_id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function getCreatedAtAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->format('Y-m-d');
    }

    public function getUpdatedAtAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->format('Y-m-d');
    }
}
