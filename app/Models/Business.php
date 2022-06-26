<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'user_id',
        'bio',
        'img',
        'bg',
        'lat',
        'lon',
        'cat_id'
    ];
    public function getImgAttribute()
    {
        return env('DEFAULT_URL')  .  '/sv/' . $this->attributes['img'];
    }
    public function getBgAttribute()
    {
        return env('DEFAULT_URL')  .  '/sv/' . $this->attributes['img'];
    }

    public function posts()
    {
        return $this->hasMany(BusinessPosts::class, 'user_id', 'user_id');
    }
}
