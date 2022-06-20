<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{

    use HasFactory;
    public $table = 'tags';
    protected $fillable = [
        'title'
    ];
    public function post()
    {
        return $this->belongsTo(Tags::class);
    }
}
