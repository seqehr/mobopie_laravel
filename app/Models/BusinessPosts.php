<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessPosts extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'caption',
        'category',
        'inputs',
        'price',
        'offer',
        'link',
    ];

    public function getInputsAttribute()
    {
        $newfiles = [];
        $files = json_decode($this->attributes['inputs']);
        if (!empty($this->attributes['inputs'])) {
        }
        foreach ($files as $file) {
            $newfiles[] = env('DEFAULT_URL') . '/sv/' . $file;
        }
        return $newfiles;
    }
}
