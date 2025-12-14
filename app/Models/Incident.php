<?php

namespace App\Models;

use App\Enums\IncidentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incident extends Model
{
    /** @use HasFactory<\Database\Factories\IncidentFactory> */
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'title',
        'description',
        'photo_url',
        'status',
    ];

    protected $casts = [
        'status' => IncidentStatus::class,
    ];

    public function resident(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resident_id');
    }
}
