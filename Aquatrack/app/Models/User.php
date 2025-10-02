<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'email',
        'phone',
        'account_number',
        'password',
        'avatar',
        'zone',
        'barangay',
        'municipality',
        'province',
        'date_installed',
        'brand',
        'serial_number',
        'size',
        'role',
        'enabled'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['avatar_url'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the full URL for the user's avatar.
     *
     * @return string|null
     */


    // app/Models/User.php
    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
            return null;
        }

        // For absolute URLs (like social login avatars)
        if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            return $this->avatar;
        }

        // For local storage - use the correct URL structure
        return asset('storage/' . $this->avatar);
    }

    public function setAccountNumberAttribute($value)
    {
        // Clean the input - remove any non-numeric characters
        $clean = preg_replace('/[^0-9]/', '', $value);

        // Format as XXX-XX-XXX if needed, or store as plain numbers
        if (strlen($clean) >= 8) {
            $this->attributes['account_number'] = substr($clean, 0, 3) . '-'
                . substr($clean, 3, 2) . '-'
                . substr($clean, 5);
        } else {
            $this->attributes['account_number'] = $clean;
        }
    }

    public function getFormattedAccountNumberAttribute()
    {
        return $this->account_number; // Already formatted from the mutator
    }

    public function setPhoneAttribute($value)
    {
        // Standardize to +63 format if it starts with 09
        $clean = str_replace('+', '', $value);
        if (str_starts_with($clean, '63')) {
            $this->attributes['phone'] = '+' . $clean;
        } elseif (str_starts_with($clean, '09')) {
            $this->attributes['phone'] = '+63' . substr($clean, 1);
        } else {
            $this->attributes['phone'] = $value;
        }
    }

    public function getFormattedPhoneAttribute()
    {
        if (str_starts_with($this->phone, '+63')) {
            return preg_replace('/(\+63)(\d{3})(\d{3})(\d{4})/', '$1 $2 $3 $4', $this->phone);
        }
        return $this->phone;
    }

    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class);
    }
}
