<?php

namespace App\Policies;

use App\Models\Scholar;
use App\Models\User;

class ScholarPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('viewScholars');
    }

    public function view(User $user, Scholar $scholar): bool
    {
        return $user->can('viewScholars');
    }

    public function create(User $user): bool
    {
        return $user->can('createScholars');
    }

    public function update(User $user, Scholar $scholar): bool
    {
        return $user->can('editScholars');
    }

    public function delete(User $user, Scholar $scholar): bool
    {
        return $user->can('deleteScholars');
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
