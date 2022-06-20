<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\LastSeen;

class Stories extends Model
{

    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'input'
    ];
    protected $appends = ['file', 'views', 'type', 'date'];
    public function views()
    {
        return $this->hasMany(StoryViews::class, 'story_id');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function getFileAttribute()
    {
        return env('DEFAULT_URL')  .  '/sv/' . $this->attributes['input'];
    }
    public function getViewsAttribute()
    {
        $views = StoryViews::where('story_id', $this->attributes['id'])->get()->count();

        return $views;
    }

    public function getTypeAttribute()
    {
        $type = substr($this->attributes['input'], strpos($this->attributes['input'], ".") + 1);
        $imgformats = ['png', 'jpg', 'webp', 'gif'];
        $videoformats = ['mp4', 'mkv',];
        if (in_array($type, $imgformats))
            $type = 'image';
        else if (in_array($type, $videoformats)) {
            $type = 'video';
        }
        return $type;
    }
    public function getDateAttribute()
    {
        $date = strtotime($this->attributes['created_at']);
        $sendtime = LastSeen::ShowLastSeen($date);
        return  $sendtime;
    }
}
