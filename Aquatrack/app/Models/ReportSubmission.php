<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'tracking_code',
        'reporter_name',
        'reporter_phone',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
