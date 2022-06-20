<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TagPeople extends Model
{
    public $table = 'tag_people';
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'post_id',
    ];
}
