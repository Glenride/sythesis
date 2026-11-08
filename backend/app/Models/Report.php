<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'input_payload' => 'array',
    ];

    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }

    public function sources()
    {
        return $this->hasMany(Source::class);
    }

    public function parent()
    {
        return $this->belongsTo(Report::class, 'parent_report_id');
    }

    public function refinements()
    {
        return $this->hasMany(Report::class, 'parent_report_id');
    }
}
