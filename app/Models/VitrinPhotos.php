<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitrinPhotos extends Model
{
    use HasFactory;
    protected $hidden = ['created_at', 'updated_at', 'user_id', 'deleted_at'];
    protected $appends = ['img'];
    protected $fillable = [
        'img',
        'user_id',
        'defalut'
    ];
    public function getImgAttribute()
    {
        return env('DEFAULT_URL')  .  '/sv/' . $this->attributes['img'];
    }
    // protected $attributes = ['image_new' => 0];
}
