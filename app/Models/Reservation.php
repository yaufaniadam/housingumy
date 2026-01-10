<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_code',
        'room_id',
        'user_id',
        'unit_kerja_id',
        'guest_name',
        'guest_phone',
        'guest_email',
        'guest_identity_number',
        'guest_type',
        'check_in_date',
        'check_out_date',
        'total_nights',
        'total_guests',
        'price_per_night',
        'total_price',
        'status',
        'notes',
        'rejection_reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'price_per_night' => 'decimal:2',
        'total_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'approved_at' => 'datetime',
        'guest_name' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Reservation $reservation) {
            if (empty($reservation->reservation_code)) {
                $reservation->reservation_code = 'RSV-' . strtoupper(Str::random(8));
            }
        });
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function checkIn(): HasOne
    {
        return $this->hasOne(CheckIn::class);
    }

    public function isInternal(): bool
    {
        return $this->guest_type === 'unit_kerja';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
