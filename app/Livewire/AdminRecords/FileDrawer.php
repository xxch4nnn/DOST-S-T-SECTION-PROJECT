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

    public bool $showStatusModal = false;

    public string $status = 'Cleared';

    #[On('open-admin-file-drawer')]
    public function openDrawer(int $recordId): void
    {
        $this->record = AdministrativeRecord::with(['creator', 'documents'])->find($recordId);

        if (! $this->record) {
            // Fallback for sample demo data
            $this->record = new AdministrativeRecord([
                'id' => $recordId,
                'title' => 'Annual Financial Report 2023',
                'record_type' => 'Financial Report',
                'series_number' => 'AFR-2023-00855',
                'year' => 2023,
                'recipient' => 'DOST Regional Office XI',
                'description' => 'Comprehensive annual financial statement for scholar disbursements and university subsidies.',
            ]);
        }

        $this->isOpen = true;
        $this->showActionMenu = false;
        $this->showStatusModal = false;
    }

    #[On('close-admin-file-drawer')]
    public function closeDrawer(): void
    {
        $this->isOpen = false;
        $this->showActionMenu = false;
        $this->showStatusModal = false;
        $this->dispatch('close-document-viewer');
    }

    public function toggleActionMenu(): void
    {
        $this->showActionMenu = ! $this->showActionMenu;
    }

    public function openStatusModal(): void
    {
        $this->showActionMenu = false;
        $this->showStatusModal = true;
    }

    public function closeStatusModal(): void
    {
        $this->showStatusModal = false;
    }

    public function updateStatus(string $newStatus): void
    {
        $this->status = ($newStatus === 'Clear') ? 'Cleared' : 'Not Cleared';
        $this->showStatusModal = false;
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
