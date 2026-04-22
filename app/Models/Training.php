<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Training extends Model
{
    protected $fillable = [
        'title', 'description', 'start_date', 'end_date',
        'location', 'quota', 'status', 'created_by',
        'qr_code', 'qr_token', 'qr_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'qr_expires_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function acceptedRegistrations(): HasMany
    {
        return $this->hasMany(Registration::class)->where('status', 'accepted');
    }

    public function availableSlots(): int
    {
        return max(0, $this->quota - $this->acceptedRegistrations()->count());
    }

    public function isFull(): bool
    {
        return $this->availableSlots() <= 0;
    }

    public function isQrValid(): bool
    {
        return $this->qr_token && $this->qr_expires_at && $this->qr_expires_at->isFuture();
    }
}
