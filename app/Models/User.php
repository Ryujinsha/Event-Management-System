<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'student_id',
        'phone',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function createdTrainings(): HasMany
    {
        return $this->hasMany(Training::class, 'created_by');
    }

    // Role helpers
    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    public function isFaculty(): bool
    {
        return $this->role?->slug === 'faculty';
    }

    public function isStudent(): bool
    {
        return $this->role?->slug === 'student';
    }

    public function isLecturer(): bool
    {
        return $this->role?->slug === 'lecturer';
    }

    public function hasRole(string $role): bool
    {
        return $this->role?->slug === $role;
    }
}
