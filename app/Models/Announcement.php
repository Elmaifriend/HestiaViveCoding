<?php

namespace App\Models;

use App\Enums\AnnouncementStatus;
use App\Enums\AnnouncementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    /** @use HasFactory<\Database\Factories\AnnouncementFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'status',
        'send_push',
    ];

    protected $casts = [
        'type' => AnnouncementType::class,
        'status' => AnnouncementStatus::class,
        'send_push' => 'boolean',
    ];
}
