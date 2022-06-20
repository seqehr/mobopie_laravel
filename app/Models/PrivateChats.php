<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrivateChats extends Model
{
    use SoftDeletes;
    use HasFactory;
    public $table = 'private_chats';
    protected $fillable = [
        'first_user',
        'secound_user'
    ];
}
