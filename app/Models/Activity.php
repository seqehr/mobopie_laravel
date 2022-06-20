<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Date;

class Activity extends Model
{
    use HasFactory;
    public $appends = ['date'];
    protected $fillable = [
        'actioner_id',
        'user_id',
        'type',
        'post_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'actioner_id')->select('id', 'img', 'name');
    }

    public function GetDateAttribute()
    {
        $date = strtotime($this->attributes['created_at']);
        return Date::ShowDate($date);
    }
}
