<?php

namespace App\Livewire\AdminRecords;

use App\Models\AdministrativeRecord;
use Livewire\Attributes\On;
use Livewire\Component;

class FileDrawer extends Component
{
    public bool $isOpen = false;

    public ?AdministrativeRecord $record = null;

    public string $activeFolderTab = 'documents';

    public bool $showActionMenu = false;

    #[On('open-admin-file-drawer')]
    public function openDrawer(int $recordId): void
    {
        $this->record = AdministrativeRecord::with(['creator', 'documents'])->find($recordId);

        if (! $this->record) {
            $this->closeDrawer();
            return;
        }

        $this->isOpen = true;
        $this->showActionMenu = false;
    }

    #[On('close-admin-file-drawer')]
    public function closeDrawer(): void
    {
        $this->isOpen = false;
        $this->showActionMenu = false;
        $this->dispatch('close-document-viewer');
    }

    public function toggleActionMenu(): void
    {
        $this->showActionMenu = ! $this->showActionMenu;
    }

    public function openDocumentViewer(): void
    {
        $title = $this->record?->title ?? 'Administrative Document';
        $this->dispatch('open-document-viewer', documentTitle: $title);
    }

    public function render()
    {
        return view('livewire.admin-records.file-drawer');
    }
}
