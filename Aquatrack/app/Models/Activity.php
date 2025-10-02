<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Traits\LogsActivity;

class Activity extends Model
{

    use LogsActivity;


    protected $guarded = [];
    protected $casts = [
        'properties' => 'array',
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function causer()
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    public static function log($event, $description, $subject = null, $properties = [])
    {
        // Prevent logging activity about activities
        if ($subject instanceof self) {
            return null;
        }

        return static::create([
            'event' => $event,
            'log_name' => $subject ? class_basename($subject) : null,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
            'causer_type' => Auth::check() ? get_class(Auth::user()) : null,
            'causer_id' => Auth::check() ? Auth::user()->id : null,
            'properties' => $properties,
        ]);
    }
}
