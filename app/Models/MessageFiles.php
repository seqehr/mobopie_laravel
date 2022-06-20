<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageFiles extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'message_id',
        'chat_id',
        'input',
        'type'
    ];

    public function message()
    {
        return $this->belongsTo(Messages::class);
    }
}
