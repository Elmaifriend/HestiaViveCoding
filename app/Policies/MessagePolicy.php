<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Message $message): bool
    {
        return $user->role === \App\Enums\UserRole::Admin || 
               $user->id === $message->sender_id || 
               $user->id === $message->receiver_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Message $message): bool
    {
        return $user->role === \App\Enums\UserRole::Admin;
    }

    public function delete(User $user, Message $message): bool
    {
        return $user->role === \App\Enums\UserRole::Admin;
    }
}
