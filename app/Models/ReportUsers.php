<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportUsers extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'reported_id',
        'category',
        'description',
        'user_id'
    ];
}
