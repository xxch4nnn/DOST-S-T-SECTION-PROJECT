<?php

namespace App\Policies;

use App\Models\Scholar;
use App\Models\User;

class ScholarPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Scholar $scholar): bool
    {
        return $user->can('viewScholars') || $user->hasRole('Super Admin');
    }

    public function create(User $user): bool
    {
        return $user->can('createScholars') || $user->hasRole('Super Admin');
    }

    public function update(User $user, Scholar $scholar): bool
    {
        return $user->can('editScholars') || $user->hasRole('Super Admin');
    }

    public function delete(User $user, Scholar $scholar): bool
    {
        return $user->can('deleteScholars') || $user->hasRole('Super Admin');
    }

    public function restore(User $user, Scholar $scholar): bool
    {
        return $this->delete($user, $scholar);
    }

    public function forceDelete(User $user, Scholar $scholar): bool
    {
        return $this->delete($user, $scholar);
    }
}
