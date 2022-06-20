<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockUser extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'block_id',
        'user_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'block_id')->select('name', 'id', 'img');
    }
}
