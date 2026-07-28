<?php

namespace App\Policies;

use App\Models\AdministrativeRecord;
use App\Models\User;

class AdminRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AdministrativeRecord $record): bool
    {
        return $user->can('viewAdminRecords') || $user->hasRole('Super Admin');
    }

    public function create(User $user): bool
    {
        return $user->can('createAdminRecords') || $user->hasRole('Super Admin');
    }

    public function update(User $user, AdministrativeRecord $record): bool
    {
        return $user->can('editAdminRecords') || $user->hasRole('Super Admin');
    }

    public function delete(User $user, AdministrativeRecord $record): bool
    {
        return $user->can('deleteAdminRecords') || $user->hasRole('Super Admin');
    }

    public function restore(User $user, AdministrativeRecord $record): bool
    {
        return $this->delete($user, $record);
    }

    public function forceDelete(User $user, AdministrativeRecord $record): bool
    {
        return $this->delete($user, $record);
    }
}
