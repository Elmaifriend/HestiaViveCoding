<?php

namespace App\Http\Controllers\Api;

use App\Enums\AnnouncementStatus;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        return Announcement::where('status', AnnouncementStatus::Published)
            ->latest()
            ->paginate(10);
    }

    public function show(Announcement $announcement)
    {
        if ($announcement->status !== AnnouncementStatus::Published) {
            abort(404);
        }
        return $announcement;
    }
}
