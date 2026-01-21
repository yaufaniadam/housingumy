<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    protected $fillable = [
        'building_id',
        'name',
        'description',
        'price',
        'capacity',
        'images',
        'is_public',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'images' => 'array',
        'is_public' => 'boolean',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'room_type_facilities')
            ->withTimestamps();
    }
}
