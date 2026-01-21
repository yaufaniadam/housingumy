<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'description',
        'unit_category',
        'show_in_search',
        'show_pricing',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_in_search' => 'boolean',
        'show_pricing' => 'boolean',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }

    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    public function shouldShowInSearch(): bool
    {
        return $this->is_active && $this->show_in_search;
    }

    public function shouldShowPricing(): bool
    {
        return $this->show_pricing;
    }
}
