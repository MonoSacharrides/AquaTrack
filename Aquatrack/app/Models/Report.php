<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'municipality',
        'province',
        'barangay',
        'purok',
        'water_issue_type',
        'custom_water_issue',
        'description',
        'user_id',
        'reporter_name',
        'reporter_phone',
        'status',
        'tracking_code',
        'zone',
        'priority',
        'latitude',
        'longitude',
        'created_at',
        'updated_at',
        'viewed_by_admin',
        'viewed_by_staff',
        'viewed_by_user',
        'update_viewed_by_admin',
        'assigned_to'
    ];

    protected $attributes = [
        'status' => 'pending',
        'priority' => 'medium',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    protected $dates = ['deleted_at']; // Ensure deleted_at is treated as a date

    /**
     * Get all photos for this report
     */
    public function photos(): HasMany
    {
        return $this->hasMany(ReportPhoto::class);
    }

    /**
     * Get the user who created this report
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function submissions()
    {
        return $this->hasMany(ReportSubmission::class);
    }

    /**
     * Get the full address attribute
     */
    public function getFullAddressAttribute(): string
    {
        return "{$this->purok}, {$this->barangay}, {$this->municipality}, {$this->province}";
    }

    /**
     * Get the reporter's name (user name or guest name)
     */
    public function getReporterNameAttribute(): string
    {
        $reporterName = $this->attributes['reporter_name'] ?? null;

        if ($reporterName) {
            return $reporterName;
        }

        if ($this->user_id) {
            return $this->user->name;
        }

        return 'Guest';
    }
}
