<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected $hidden = [
        'raw_content',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
