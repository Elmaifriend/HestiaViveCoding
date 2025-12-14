<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;

class IncidentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Incident $incident): bool
    {
        return $user->role === \App\Enums\UserRole::Admin || $user->id === $incident->resident_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Incident $incident): bool
    {
        return $user->role === \App\Enums\UserRole::Admin;
    }

    public function delete(User $user, Incident $incident): bool
    {
        return $user->role === \App\Enums\UserRole::Admin;
    }
}
