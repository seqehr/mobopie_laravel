<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Posts;
use App\Models\Tags;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostTags extends Model
{

    public $table = 'post_tags';
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'post_id',
        'tag_id',
        'date',
    ];

    public function post()
    {
        return $this->belongsTo(Posts::class);
    }
    public function tags()
    {
        return $this->belongsTo(Tags::class, 'tag_id');
    }
}
