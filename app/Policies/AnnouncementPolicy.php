<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Admin can see all, residents see via API (filtered)
    }

    public function view(User $user, Announcement $announcement): bool
    {
        return $user->role === \App\Enums\UserRole::Admin;
    }

    public function create(User $user): bool
    {
        return $user->role === \App\Enums\UserRole::Admin;
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->role === \App\Enums\UserRole::Admin;
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->role === \App\Enums\UserRole::Admin;
    }
}
