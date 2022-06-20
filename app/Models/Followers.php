<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Followers extends Model
{
    use SoftDeletes;
    public $table = 'followers';
    protected $hidden = ['follower_id', 'following_id', 'date'];
    use HasFactory;
    protected $fillable = [
        'follower_id',
        'following_id',
        'status',
        'id'
    ];

    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id')->select('name', 'id', 'img');
    }
    public function following()
    {
        return $this->belongsTo(User::class, 'following_id')->select('name', 'id', 'img');
    }
}
