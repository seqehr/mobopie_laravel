<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessFollowers extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'follower_id',
        'business_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'follower_id')->select('id', 'name', 'img');
    }
}
