<?php

namespace App\Models;

use App\Enums\InvitationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    /** @use HasFactory<\Database\Factories\InvitationFactory> */
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'qr_code',
        'expiration_date',
        'status',
    ];

    protected $casts = [
        'expiration_date' => 'datetime',
        'status' => InvitationStatus::class,
    ];

    public function resident(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resident_id');
    }
}
