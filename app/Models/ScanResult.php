<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScanResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'scan_id',
        'title',
        'severity',
        'raw_data',
        'description',
        'recommendation',
    ];

    protected $casts = [
        'raw_data' => 'array',
    ];

    public function scan()
    {
        return $this->belongsTo(Scan::class);
    }
}