<?php

namespace App\Policies;

use App\Models\GateEntry;
use App\Models\User;

class GateEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, GateEntry $gateEntry): bool
    {
        return $user->role === \App\Enums\UserRole::Admin || $user->id === $gateEntry->resident_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, GateEntry $gateEntry): bool
    {
        return $user->role === \App\Enums\UserRole::Admin;
    }

    public function delete(User $user, GateEntry $gateEntry): bool
    {
        return $user->role === \App\Enums\UserRole::Admin;
    }
}
