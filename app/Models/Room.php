<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'room_type_id', // Added
        'room_number',
        'room_type', // Deprecated
        'floor',
        'capacity', // Optional override
        'price',    // Optional override
        'status',
        'description', // Deprecated
        'image',       // Deprecated
        'is_daily_rentable',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_daily_rentable' => 'boolean',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'room_facilities')
            ->withTimestamps();
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function getEffectivePriceAttribute()
    {
        return $this->price ?? $this->roomType?->price ?? 0;
    }

    public function getEffectiveCapacityAttribute()
    {
        return $this->capacity ?? $this->roomType?->capacity ?? 0;
    }
}
