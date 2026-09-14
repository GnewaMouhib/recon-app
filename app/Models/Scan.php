<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Scan extends Model
{
    use HasFactory;

    protected $fillable = [
        'target_id',
        'type',
        'status',
        'started_at',
        'finished_at',
        'error_message',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function target()
    {
        return $this->belongsTo(Target::class);
    }

    public function results()
    {
        return $this->hasMany(ScanResult::class);
    }
}