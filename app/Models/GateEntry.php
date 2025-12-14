<?php

namespace App\Models;

use App\Enums\GateEntryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GateEntry extends Model
{
    /** @use HasFactory<\Database\Factories\GateEntryFactory> */
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'guest_name',
        'entry_date',
        'status',
    ];

    protected $casts = [
        'entry_date' => 'datetime',
        'status' => GateEntryStatus::class,
    ];

    public function resident(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resident_id');
    }
}
