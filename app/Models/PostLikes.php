<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostLikes extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'post_id',
        'user_id',
    ];
    public $table = 'post_likes';
}
