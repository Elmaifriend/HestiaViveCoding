<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationFactory> */
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'amenity_id',
        'date',
        'end_time',
        'status',
    ];

    protected $casts = [
        'date' => 'datetime',
        'end_time' => 'datetime',
        'status' => ReservationStatus::class,
    ];

    public function resident(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resident_id');
    }

    public function amenity(): BelongsTo
    {
        return $this->belongsTo(Amenity::class);
    }
}
