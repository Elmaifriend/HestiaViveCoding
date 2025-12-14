<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->role === \App\Enums\UserRole::Admin || $user->id === $payment->resident_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->role === \App\Enums\UserRole::Admin;
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->role === \App\Enums\UserRole::Admin;
    }
}
