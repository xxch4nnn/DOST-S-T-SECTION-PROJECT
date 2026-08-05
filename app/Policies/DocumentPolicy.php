<?php

namespace App\Policies;

use App\Models\AdministrativeRecord;
use App\Models\Document;
use App\Models\Scholar;
use App\Models\User;

class DocumentPolicy
{
    public function view(User $user, Document $document): bool
    {
        return $this->download($user, $document);
    }

    public function download(User $user, Document $document): bool
    {
        $related = $document->documentable;

        if ($related instanceof Scholar) {
            return $user->can('viewScholars') || $user->can('uploadDocuments');
        }

        if ($related instanceof AdministrativeRecord) {
            return $user->can('viewAdminRecords') || $user->can('uploadDocuments');
        }

        return $user->can('uploadDocuments');
    }

    public function update(User $user, Document $document): bool
    {
        return $user->can('editDocumentMetadata');
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->can('strikeOffDocuments');
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
