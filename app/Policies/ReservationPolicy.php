<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Reservation $reservation): bool
    {
        return $user->role === \App\Enums\UserRole::Admin || $user->id === $reservation->resident_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return $user->role === \App\Enums\UserRole::Admin;
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        return $user->role === \App\Enums\UserRole::Admin;
    }
}
