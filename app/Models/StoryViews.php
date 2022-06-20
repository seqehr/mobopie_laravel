<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoryViews extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $table = 'story_views';
    protected $fillable = [
        'user_id',
        'story_id',
    ];
}
