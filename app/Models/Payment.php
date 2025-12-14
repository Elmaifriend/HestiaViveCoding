<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'amount',
        'status',
        'receipt_url',
        'date_paid',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => PaymentStatus::class,
        'date_paid' => 'datetime',
    ];

    public function resident(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resident_id');
    }
}
