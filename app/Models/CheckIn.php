<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CheckIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'qr_code',
        'qr_image',
        'checked_in_at',
        'checked_out_at',
        'checked_in_by',
        'checked_out_by',
        'check_in_notes',
        'check_out_notes',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (CheckIn $checkIn) {
            if (empty($checkIn->qr_code)) {
                $checkIn->qr_code = 'QR-' . strtoupper(Str::random(12));
            }
        });
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    public function hasCheckedIn(): bool
    {
        return $this->checked_in_at !== null;
    }

    public function hasCheckedOut(): bool
    {
        return $this->checked_out_at !== null;
    }
}
