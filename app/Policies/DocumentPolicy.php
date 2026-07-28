<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function view(User $user, Document $document): bool
    {
        // Temporarily permissive while ownership model is finalized.
        return true;
    }

    public function download(User $user, Document $document): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        $related = $document->documentable;

        if ($related instanceof \App\Models\Scholar && $user->can('viewScholars')) {
            return true;
        }

        if ($related instanceof \App\Models\AdministrativeRecord && $user->can('viewAdminRecords')) {
            return true;
        }

        return false;
    }

    public function update(User $user, Document $document): bool
    {
        return $user->can('editDocumentMetadata') || $user->hasRole('Super Admin');
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->can('strikeOffDocuments') || $user->hasRole('Super Admin');
    }

    public function restore(User $user, Document $document): bool
    {
        return $this->delete($user, $document);
    }

    public function forceDelete(User $user, Document $document): bool
    {
        return $this->delete($user, $document);
    }
}
