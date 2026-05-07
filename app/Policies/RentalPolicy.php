<?php

namespace App\Policies;

use App\Models\Rental;
use App\Models\User;

class RentalPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Rental $rental): bool
    {
        return $user->id === $rental->user_id || $user->type === 'admin';
    }

    public function delete(User $user, Rental $rental): bool
    {
        return $user->id === $rental->user_id || $user->type === 'admin';
    }
}
