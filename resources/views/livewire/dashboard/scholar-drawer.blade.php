<?php

use App\Models\Scholar;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component
{
    public bool $isOpen = false;
    public ?int $scholarId = null;
    public ?array $scholarData = null;

    public string $activeFolderTab = 'Amendatory Agreement';
    public array $expandedFolders = ['Amendatory Agreement'];

    public bool $showActionMenu = false;
    public bool $showStatusModal = false;

    #[On('open-scholar-drawer')]
    public function openDrawer($scholarId, ?array $scholarData = null): void
    {
        $this->scholarId = (int) $scholarId;
        $this->isOpen = true;
        
        if ($scholarData) {
            $this->scholarData = $scholarData;
        } else {
            $this->loadScholar();
        }
    }

    public function closeDrawer(): void
    {
        $this->isOpen = false;
        $this->showActionMenu = false;
        $this->showStatusModal = false;
        $this->dispatch('close-document-viewer');
    }

    public function toggleActionMenu(): void
    {
        $this->showActionMenu = !$this->showActionMenu;
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
        if ($this->scholarData) {
            $statusValue = in_array($newStatus, ['Clear', 'Cleared'], true) ? 'Cleared' : 'Not Cleared';
            $this->scholarData['status'] = $statusValue;
            if ($this->scholarId) {
                $dbScholar = Scholar::find($this->scholarId);
                if ($dbScholar) {
                    $statusModel = \App\Models\ClearanceStatus::where('name', $statusValue)->first();
                    if ($statusModel) {
                        $dbScholar->clearance_status_id = $statusModel->id;
                        $dbScholar->save();
                    }
                }
            }
        }
        $this->showStatusModal = false;
    }

    public function toggleFolder(string $folderName): void
    {
        if (in_array($folderName, $this->expandedFolders, true)) {
            $this->expandedFolders = array_diff($this->expandedFolders, [$folderName]);
        } else {
            $this->expandedFolders[] = $folderName;
        }
    }

    public function openDocument(string $title, string $imageUrl = '', string $fileType = 'pdf', int $documentIndex = 1, int $totalPages = 1): void
    {
        $this->dispatch('open-document-viewer',
            title: $title,
            fileUrl: $imageUrl,
            scholarName: $this->scholarData['name'] ?? 'Maclang, Wakin Cean C.',
            scholarId: $this->scholarId ?? 1,
            fileId: 4, // Mock File ID for now
            fileType: $fileType,
            documentIndex: $documentIndex,
            extraData: [
                'totalPages' => $totalPages,
                'currentPage' => 1,
            ]
        );
    }

    public function loadScholar(): void
    {
        $dbScholar = Scholar::with(['scholarshipType', 'school', 'course', 'region', 'clearanceStatus'])
            ->find($this->scholarId);

        if ($dbScholar) {
            $this->scholarData = [
                'name' => "{$dbScholar->last_name}, {$dbScholar->first_name} {$dbScholar->middle_name}",
                'spas_id' => $dbScholar->spas_no ?? '2023-00855-2235',
                'program' => $dbScholar->scholarshipType->code ?? 'RA 10612',
                'program_type' => $dbScholar->scholarshipType->name ?? 'DOST - SEI Undergraduate Scholarship',
                'year_of_award' => $dbScholar->year_of_award ?? '2023',
                'clearance_date' => $dbScholar->clearance_date ? $dbScholar->clearance_date->format('d / m / Y') : '23 / 06 / 2027',
                'course' => $dbScholar->course->name ?? 'BS in Computer Science Major in Data Science',
                'university' => $dbScholar->school->name ?? 'University of Southeastern Philippines',
                'address' => $dbScholar->barangay ? "{$dbScholar->barangay}, {$dbScholar->district}" : 'Brgy. 34 - D, C.M. Recto St. Poblacion District',
                'municipality' => $dbScholar->municipality ?? 'Davao City',
                'province' => $dbScholar->province ?? 'Davao del Sur',
                'region' => $dbScholar->region->name ?? 'Region XI - Davao Region',
                'email' => $dbScholar->email_address ?? 'maclangw26@gmail.com',
                'contact' => $dbScholar->contact_number ?? '09762941445',
                'birthdate' => $dbScholar->birthdate ? $dbScholar->birthdate->format('m / d / Y') : '08 / 23 / 2005',
                'sex' => $dbScholar->sex ?? 'Male',
                'status' => $dbScholar->clearanceStatus->name ?? 'Not Cleared',
            ];
        } else {
            // Demonstration mock data matching Screenshots 2 & 3
            $this->scholarData = [
                'name' => 'Maclang, Wakin Cean C.',
                'spas_id' => '2023-00855-2235',
                'program' => 'RA 10612',
                'program_type' => 'DOST - SEI Undergraduate Scholarship',
                'year_of_award' => '2023',
                'clearance_date' => '23 / 06 / 2027',
                'course' => 'BS in Computer Science Major in Data Science',
                'university' => 'University of Southeastern Philippines',
                'address' => 'Brgy. 34 - D, C.M. Recto St. Poblacion District',
                'municipality' => 'Davao City',
                'province' => 'Davao del Sur',
                'region' => 'Region XI - Davao Region',
                'email' => 'maclangw26@gmail.com',
                'contact' => '09762941445',
                'birthdate' => '08 / 23 / 2005',
                'sex' => 'Male',
                'status' => 'Not Cleared',
            ];
        }
    }
}; ?>

<div>
    {{-- Slide-over Backdrop Overlay --}}
    @if($isOpen)
        <div wire:click="closeDrawer" class="scholar-drawer-backdrop"></div>
    @endif

    {{-- Slide-over Right Panel with Gray Header & White Folder Body --}}
    <div class="scholar-drawer {{ $isOpen ? 'scholar-drawer--open' : '' }}">
        @if($scholarData)
            {{-- Gray Header Bar with White Protruding Action Tab --}}
            <div class="scholar-drawer__top-bar">
                <div class="scholar-drawer__action-tab">
                    <button type="button" class="btn btn-link text-muted p-1 border-0 shadow-none text-decoration-none" title="Download">
                        <i class="ph ph-download-simple fs-5"></i>
                    </button>
                    <button type="button" class="btn btn-link text-muted p-1 border-0 shadow-none text-decoration-none me-3" title="Print">
                        <i class="ph ph-printer fs-5"></i>
                    </button>
                    <button wire:click="closeDrawer" type="button" class="btn btn-link text-muted p-1 border-0 shadow-none text-decoration-none" title="Close">
                        <i class="ph ph-x fs-5"></i>
                    </button>
                </div>
            </div>

            {{-- Main White Folder Body Content Card --}}
            <div class="scholar-drawer__white-card">
                {{-- Header Title & Badges --}}
                <div class="scholar-drawer__header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="scholar-drawer__title">{{ $scholarData['name'] }}</h2>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge {{ in_array(($scholarData['status'] ?? ''), ['Cleared', 'Clear'], true) ? 'badge-status-cleared' : 'badge-status-not-cleared' }} rounded-pill px-3 py-1 fs-7">
                                {{ $scholarData['status'] }}
                            </span>
                            <div class="scholar-action-dropdown-wrapper">
                                <button wire:click="toggleActionMenu" type="button" class="btn btn-link text-muted p-0 border-0 shadow-none text-decoration-none">
                                    <i class="ph ph-dots-three-vertical fs-5"></i>
                                </button>
                                @if($showActionMenu)
                                    <div class="scholar-action-dropdown">
                                        <button wire:click="openStatusModal" type="button" class="dropdown-item-btn">
                                            <i class="ph ph-arrows-counter-clockwise fs-6 text-primary"></i>
                                            <span>Update Status</span>
                                        </button>
                                        <button type="button" class="dropdown-item-btn">
                                            <i class="ph ph-pencil-simple fs-6 text-secondary"></i>
                                            <span>Edit Scholar</span>
                                        </button>
                                        <button type="button" class="dropdown-item-btn dropdown-item-btn--danger">
                                            <i class="ph ph-trash fs-6 text-danger"></i>
                                            <span>Delete File</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="scholar-drawer__spas">
                        <span class="fw-bold text-dark">SPAS ID:</span>
                        <span>{{ $scholarData['spas_id'] }}</span>
                    </div>
                </div>

                <hr class="my-3 text-secondary opacity-25">

                {{-- Metadata 2-Column Grid --}}
                <div class="scholar-drawer__grid">
                    <div class="scholar-meta-item">
                        <label>Scholarship Program</label>
                        <value>{{ $scholarData['program'] }}</value>
                    </div>
                    <div class="scholar-meta-item">
                        <label>Scholarship Program Type</label>
                        <value>{{ $scholarData['program_type'] }}</value>
                    </div>

                    <div class="scholar-meta-item">
                        <label>Year of Award</label>
                        <value>{{ $scholarData['year_of_award'] }}</value>
                    </div>
                    <div class="scholar-meta-item">
                        <label>Clearance Date</label>
                        <value>{{ $scholarData['clearance_date'] }}</value>
                    </div>

                    <div class="scholar-meta-item">
                        <label>Course</label>
                        <value>{{ $scholarData['course'] }}</value>
                    </div>
                    <div class="scholar-meta-item">
                        <label>University</label>
                        <value>{{ $scholarData['university'] }}</value>
                    </div>

                    <div class="scholar-meta-item">
                        <label>Address</label>
                        <value>{{ $scholarData['address'] }}</value>
                    </div>
                    <div class="scholar-meta-item">
                        <label>Municipality</label>
                        <value>{{ $scholarData['municipality'] }}</value>
                    </div>

                    <div class="scholar-meta-item">
                        <label>Province</label>
                        <value>{{ $scholarData['province'] }}</value>
                    </div>
                    <div class="scholar-meta-item">
                        <label>Region</label>
                        <value>{{ $scholarData['region'] }}</value>
                    </div>

                    <div class="scholar-meta-item">
                        <label>Email</label>
                        <value>{{ $scholarData['email'] }}</value>
                    </div>
                    <div class="scholar-meta-item">
                        <label>Contact Number</label>
                        <value>{{ $scholarData['contact'] }}</value>
                    </div>

                    <div class="scholar-meta-item">
                        <label>Birthdate</label>
                        <value>{{ $scholarData['birthdate'] }}</value>
                    </div>
                    <div class="scholar-meta-item">
                        <label>Sex</label>
                        <value>{{ $scholarData['sex'] }}</value>
                    </div>
                </div>

                {{-- Bottom Section: Scanned Files Accordion --}}
                <div class="scholar-drawer__scanned-section">
                    <div class="d-flex justify-content-end mb-2">
                        <span class="small text-muted fw-medium">Scanned Files</span>
                    </div>

                    {{-- Dynamic Document Folders Array (Backend Handoff Ready with Test Cases A & B) --}}
                    @php
                        $fileGroups = [
                            [
                                'name' => 'Amendatory Agreement',
                                'type' => 'pdf',
                                'items' => [
                                    ['title' => 'Amendatory Agreement', 'sub' => 'Document 1', 'index' => 1, 'totalPages' => 10, 'url' => ''],
                                    ['title' => 'Amendatory Agreement', 'sub' => 'Document 2', 'index' => 2, 'totalPages' => 10, 'url' => ''],
                                    ['title' => 'Amendatory Agreement', 'sub' => 'Document 3', 'index' => 3, 'totalPages' => 10, 'url' => ''],
                                ]
                            ],
                            [
                                'name' => 'Report of Grades',
                                'type' => 'pdf',
                                'items' => [
                                    ['title' => 'Report of Grades', 'sub' => 'Document 1', 'index' => 1, 'totalPages' => 1, 'url' => 'https://images.unsplash.com/photo-1568667256549-094345857637?q=80&w=1000&auto=format&fit=crop'],
                                ]
                            ]
                        ];
                    @endphp

                    @foreach($fileGroups as $group)
                        <div class="folder-accordion mb-2">
                            <button wire:click="toggleFolder('{{ $group['name'] }}')" type="button" class="folder-tab-btn">
                                <span>{{ $group['name'] }}</span>
                            </button>

                            @if(in_array($group['name'], $expandedFolders, true))
                                <div class="folder-accordion__content">
                                    <div class="d-flex gap-3 overflow-auto pb-2">
                                        @foreach($group['items'] as $docItem)
                                            <div wire:click="openDocument('{{ $docItem['title'] }}', '{{ $docItem['url'] }}', '{{ $group['type'] }}', {{ $docItem['index'] }}, {{ $docItem['totalPages'] }})" class="doc-thumbnail-card" role="button">
                                                <div class="doc-thumbnail-card__preview">
                                                    <div class="doc-mini-paper">
                                                        <div class="doc-mini-header">
                                                            <div class="doc-mini-logo"></div>
                                                            <div class="doc-mini-title">{{ $docItem['title'] }}</div>
                                                            <div class="doc-mini-sub">{{ $docItem['sub'] }}</div>
                                                        </div>
                                                        <div class="doc-mini-body">
                                                            <div class="doc-mini-line"></div>
                                                            <div class="doc-mini-line short"></div>
                                                            <div class="doc-mini-table"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Update Status Modal --}}
    @if($showStatusModal)
        <div class="status-modal-backdrop" wire:click="closeStatusModal">
            <div class="status-modal-card" wire:click.stop>
                <div class="status-modal__header">
                    <h5 class="fw-bold">Update File Status</h5>
                    <button wire:click="closeStatusModal" type="button" class="btn btn-link text-muted p-1 border-0 shadow-none">
                        <i class="ph ph-x fs-5"></i>
                    </button>
                </div>
                <div class="status-modal__actions">
                    <button wire:click="updateStatus('Not Cleared')" type="button" class="btn-status-option btn-status-option--not-cleared">
                        Not Cleared
                    </button>
                    <button wire:click="updateStatus('Clear')" type="button" class="btn-status-option btn-status-option--clear">
                        Cleared
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

