<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Target extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'domain',
        'url',
        'is_authorized',
        'authorization_note',
    ];

    protected $casts = [
        'is_authorized' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function scans()
    {
        return $this->hasMany(Scan::class);
    }
}