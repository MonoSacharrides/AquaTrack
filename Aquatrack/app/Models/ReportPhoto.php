<?php
// app/Models/ReportPhoto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'path',
        'original_name',
        'mime_type',
        'size',
        'type',
    ];

    protected $casts = [
        'type' => 'string'
    ];

    /**
     * Get the report that owns this photo
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get the full URL to the photo
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }
}
