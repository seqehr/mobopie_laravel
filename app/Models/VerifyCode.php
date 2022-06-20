<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifyCode extends Model
{
    protected  $table = 'verifycodes';
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'code',
        'email',
    ];
}
