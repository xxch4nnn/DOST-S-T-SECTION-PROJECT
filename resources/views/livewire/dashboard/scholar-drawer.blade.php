<?php

<<<<<<< HEAD
use App\Models\File;
=======
use App\Models\Document;
>>>>>>> 079526efeb8c1beb8e0742515d01b57dc18ee614
use App\Models\Scholar;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component
{
    public bool $isOpen = false;
    public ?int $scholarId = null;
    public ?array $scholarData = null;
    public array $fileGroups = [];

    public string $activeFolderTab = 'Amendatory Agreement';
    public array $expandedFolders = ['Amendatory Agreement', 'Report of Grades'];

    public bool $showActionMenu = false;
    public bool $showStatusModal = false;
    public array $file_groups = [];
    public array $file_types = [];

    public function mount(): void
    {
        if (request()->has('open_scholar')) {
            $this->openDrawer((int) request()->get('open_scholar'));
        } elseif (session()->has('open_scholar_id')) {
            $this->openDrawer((int) session()->get('open_scholar_id'));
        }
    }

    #[On('open-scholar-drawer')]
    public function openDrawer(int|string $scholarId, ?array $scholarData = null): void
    {
        $this->scholarId = (int) $scholarId;
        $this->isOpen = true;
        if ($scholarData) {
            $scholarData['id'] = $this->scholarId;
            $this->scholarData = $scholarData;
        }
        $this->loadScholar();
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
            $this->expandedFolders = array_values(array_diff($this->expandedFolders, [$folderName]));
        } else {
            $this->expandedFolders[] = $folderName;
        }
    }

<<<<<<< HEAD
    public function openDocument(string $folderName, int $index): void
=======
    public function openDocument(string $title, string $imageUrl = '', string $fileType = 'pdf', int $documentIndex = 1, int $totalPages = 1, int $fileId = 0): void
>>>>>>> 079526efeb8c1beb8e0742515d01b57dc18ee614
    {
        $folderFiles = $this->file_groups[$folderName] ?? [];
        $file = $folderFiles[$index] ?? null;

        if (!$file) {
            return;
        }

        // Generate secure authenticated route URL for local private storage file
        $secureStreamUrl = route('documents.view', ['file' => $file['id']]);

        $this->dispatch('open-document-viewer',
<<<<<<< HEAD
            title: (string) ($file['file_type_name'] ?? $file['file_name'] ?? 'Document'),
            fileUrl: (string) $secureStreamUrl,
            scholarName: (string) ($this->scholarData['name'] ?? 'Scholar'),
            fileType: str_contains($file['mime_type'] ?? '', 'image') ? 'image' : 'pdf',
            documentIndex: (int) ($index + 1),
            scholarId: (int) ($this->scholarId ?? -1),
            fileId: (int) ($file['id'] ?? 4),
=======
            title: $title,
            fileUrl: $imageUrl,
            scholarName: $this->scholarData['name'] ?? 'Maclang, Wakin Cean C.',
            fileType: $fileType,
            documentIndex: $documentIndex,
            scholarId: $this->scholarId ?? 1,
            fileId: $fileId,
            extraData: [
                'totalPages' => $totalPages,
                'currentPage' => 1,
            ]
>>>>>>> 079526efeb8c1beb8e0742515d01b57dc18ee614
        );
    }


    public function loadScholar(): void
    {
<<<<<<< HEAD
        $dbScholar = Scholar::with(['scholarshipProgramType', 'school', 'course', 'region', 'clearanceStatus'])
            ->find($this->scholarId);
        
        // load all files
        
        $files = File::fromQuery("
            SELECT 
                f.id,
                ft.name as file_type_name,
                f.file_name,
                f.file_path,
                f.updated_at,
                f.file_size,
                f.mime_type,
                f.metadata
            FROM files as f
            INNER JOIN file_types as ft ON f.file_type_id = ft.id
            WHERE JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.scholar_id')) = :scholarId
            ORDER BY ft.name ASC
        ", array('scholarId' => $this->scholarId))->toArray();

        $this->file_groups = collect($files)->groupBy('file_type_name')->toArray();
        // dd($this->file_groups);

        if ($dbScholar) {
            $this->scholarData = [
                'name' => "{$dbScholar->last_name}, {$dbScholar->first_name} {$dbScholar->middle_name}",
                'spas_id' => $dbScholar->spas_number ?? 'null',
                'program' => $dbScholar->scholarshipProgram->name ??  'null',
                'program_type' => $dbScholar->scholarshipProgramType->name ?? 'null',
                'year_of_award' => $dbScholar->year_of_award ?? 'null',
                'clearance_date' => $dbScholar->clearance_date ? \Carbon\Carbon::parse($dbScholar->clearance_date, 'Asia/Manila')->format('m / d / Y') : 'None (Not Cleared)',
                'course' => $dbScholar->course->name ?? 'null',
                'university' => $dbScholar->school->name ? ($dbScholar->school->abbreviation ? ($dbScholar->school->name . ' (' . $dbScholar->school->abbreviation . ')') : $dbScholar->school->name) : 'null',
                'address' => $dbScholar->barangay ? "{$dbScholar->barangay}, {$dbScholar->district}" : 'null',
=======
        $dbScholar = Scholar::with([
            'scholarship',
            'scholarshipType',
            'school',
            'course',
            'region',
            'clearanceStatus',
            'documents.fileType',
        ])->find($this->scholarId);

        if ($dbScholar) {
            $this->scholarData = [
                'id' => $dbScholar->id,
                'name' => trim("{$dbScholar->last_name}, {$dbScholar->first_name} ".($dbScholar->middle_name ?? '')),
                'spas_id' => $dbScholar->spas_no ?? 'null',
                'program' => $dbScholar->program ?? ($dbScholar->scholarship->name ?? 'null'),
                'program_type' => $dbScholar->scholarshipType->name ?? 'null',
                'year_of_award' => (string) ($dbScholar->year_of_award ?? 'null'),
                'clearance_date' => $dbScholar->clearance_date ? \Carbon\Carbon::parse($dbScholar->clearance_date, 'Asia/Manila')->format('m / d / Y') : 'None (Not Cleared)',
                'course' => $dbScholar->course->name ?? 'null',
                'university' => $dbScholar->school ? ($dbScholar->school->abbreviation ? ($dbScholar->school->name.' ('.$dbScholar->school->abbreviation.')') : $dbScholar->school->name) : 'null',
                'address' => $dbScholar->barangay ? "{$dbScholar->barangay}".($dbScholar->district ? ", {$dbScholar->district}" : '') : 'null',
>>>>>>> 079526efeb8c1beb8e0742515d01b57dc18ee614
                'municipality' => $dbScholar->municipality ?? 'null',
                'province' => $dbScholar->province ?? 'null',
                'region' => $dbScholar->region->name ?? 'null',
                'email' => $dbScholar->email_address ?? 'null',
                'contact' => $dbScholar->contact_number ?? 'null',
                'birthdate' => $dbScholar->birthdate ? \Carbon\Carbon::parse($dbScholar->birthdate)->format('m / d / Y') : 'null',
                'sex' => $dbScholar->sex ?? 'null',
                'status' => $dbScholar->clearanceStatus->name ?? 'null',
            ];

            // Dynamically load and group active documents
            $documents = $dbScholar->documents->where('status', 'active');
            if ($documents->isNotEmpty()) {
                $grouped = [];
                foreach ($documents as $doc) {
                    $typeName = $doc->fileType->name ?? 'General Documents';
                    if (!isset($grouped[$typeName])) {
                        $isImage = str_contains($doc->mime_type ?? '', 'image');
                        $grouped[$typeName] = [
                            'name' => $typeName,
                            'type' => $isImage ? 'image' : 'pdf',
                            'items' => [],
                        ];
                    }
                    $index = count($grouped[$typeName]['items']) + 1;
                    $grouped[$typeName]['items'][] = [
                        'id' => $doc->id,
                        'title' => $typeName,
                        'sub' => $doc->original_filename ?: "Document {$index}",
                        'index' => $index,
                        'totalPages' => 1,
                        'url' => route('documents.download', $doc->id),
                    ];
                }
                $this->fileGroups = array_values($grouped);
                $this->expandedFolders = array_keys($grouped);
            } else {
                $this->fileGroups = [];
            }
        } elseif (!$this->scholarData) {
            // Demonstration mock data matching Screenshots
            $this->scholarData = [
                'id' => $this->scholarId ?? 1,
                'name' => 'Maclang, Wakin Cean C.',
                'spas_id' => 'U-2023-00855-2235',
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
            $this->fileGroups = [];
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
                                        <a href="{{ route('scholars.edit', $scholarData['id'] ?? $scholarId ?? 1) }}" wire:navigate class="dropdown-item-btn text-decoration-none">
                                            <i class="ph ph-pencil-simple fs-6 text-secondary"></i>
                                            <span>Edit Scholar</span>
                                        </a>
                                        <button type="button" class="dropdown-item-btn dropdown-item-btn--danger">
                                            <i class="ph ph-trash fs-6 text-danger"></i>
                                            <span>Delete Scholar</span>
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
                        <label>Barangay/District</label>
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

<<<<<<< HEAD
                    @foreach ($file_groups as $folderName => $items)
                        <div class="folder-accordion mb-2">
                            <button wire:click="toggleFolder('{{ $folderName }}')" type="button" class="folder-tab-btn">
                                <span>{{ $folderName }}</span>
                            </button>

                            @if(in_array($folderName, $expandedFolders, true))
                                <div class="folder-accordion__content">
                                    <div class="d-flex gap-3 overflow-auto pb-2">
                                        @foreach ($items as $index => $file)
                                            <div wire:click="openDocument('{{ $folderName }}', {{ $index }})" class="doc-thumbnail-card" role="button">
                                                <div class="doc-thumbnail-card__preview">
                                                    <div class="doc-mini-paper">
                                                        <div class="doc-mini-header">
                                                            <div class="doc-mini-logo"></div>
                                                            <div class="doc-mini-title">{{ $file['file_type_name'] }}</div>
                                                            <div class="doc-mini-sub">{{ $file['file_name'] }}</div>
                                                        </div>
                                                        <div class="doc-mini-body">
                                                            <div class="doc-mini-line"></div>
                                                            <div class="doc-mini-line short"></div>
                                                            <div class="doc-mini-table"></div>
=======
                    @php
                        $displayFileGroups = $fileGroups;
                        if (empty($displayFileGroups) && !$scholarId) {
                            $displayFileGroups = [
                                [
                                    'name' => 'Amendatory Agreement',
                                    'type' => 'pdf',
                                    'items' => [
                                        ['id' => 1, 'title' => 'Amendatory Agreement', 'sub' => 'Document 1', 'index' => 1, 'totalPages' => 10, 'url' => ''],
                                        ['id' => 2, 'title' => 'Amendatory Agreement', 'sub' => 'Document 2', 'index' => 2, 'totalPages' => 10, 'url' => ''],
                                        ['id' => 3, 'title' => 'Amendatory Agreement', 'sub' => 'Document 3', 'index' => 3, 'totalPages' => 10, 'url' => ''],
                                    ]
                                ],
                                [
                                    'name' => 'Report of Grades',
                                    'type' => 'pdf',
                                    'items' => [
                                        ['id' => 4, 'title' => 'Report of Grades', 'sub' => 'Document 1', 'index' => 1, 'totalPages' => 1, 'url' => 'https://images.unsplash.com/photo-1568667256549-094345857637?q=80&w=1000&auto=format&fit=crop'],
                                    ]
                                ]
                            ];
                        }
                    @endphp

                    @if(empty($displayFileGroups))
                        <div class="text-center py-4 text-muted">
                            <i class="ph ph-folder-dashed fs-2 d-block mb-1 opacity-50"></i>
                            <span class="small">No scanned files uploaded for this scholar yet.</span>
                        </div>
                    @else
                        @foreach($displayFileGroups as $group)
                            <div class="folder-accordion mb-2">
                                <button wire:click="toggleFolder('{{ $group['name'] }}')" type="button" class="folder-tab-btn">
                                    <span>{{ $group['name'] }}</span>
                                </button>

                                @if(in_array($group['name'], $expandedFolders, true))
                                    <div class="folder-accordion__content">
                                        <div class="d-flex gap-3 overflow-auto pb-2">
                                            @foreach($group['items'] as $docItem)
                                                <div wire:click="openDocument('{{ $docItem['title'] }}', '{{ $docItem['url'] }}', '{{ $group['type'] }}', {{ $docItem['index'] }}, {{ $docItem['totalPages'] }}, {{ $docItem['id'] ?? 0 }})" class="doc-thumbnail-card" role="button">
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
>>>>>>> 079526efeb8c1beb8e0742515d01b57dc18ee614
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif
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

