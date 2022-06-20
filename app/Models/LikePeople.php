<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikePeople extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_user',
        'secound_user',
        'flike',
        'slike'
    ];
}
