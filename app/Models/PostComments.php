<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostComments extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'text'
    ];
    public $table = 'post_comments';
    // protected $appends = ['username'];
    public function replies()
    {
        return $this->hasMany(PostComments::class, 'parent_id');
    }
    public function post()
    {
        return $this->belongsTo(Posts::class, 'post_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    // public function getUsernameAttribute()
    // {
    //     return $this->attributes['username'] = $this->belongsTo(User::class, 'user_id');
    // }
}
