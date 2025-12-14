<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InvitationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Invitation $invitation): bool
    {
        return $user->role === \App\Enums\UserRole::Admin || $user->id === $invitation->resident_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Invitation $invitation): bool
    {
        return $user->role === \App\Enums\UserRole::Admin;
    }

    public function delete(User $user, Invitation $invitation): bool
    {
        return $user->role === \App\Enums\UserRole::Admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Invitation $invitation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Invitation $invitation): bool
    {
        return false;
    }
}
