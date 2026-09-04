<?php

use App\Models\Document;
use App\Models\Scholar;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

new class extends Component
{
    public bool $isOpen = false;
    public ?int $scholarId = null;

    public string $activeFolderTab = 'Amendatory Agreement';
    public array $expandedFolders = [];

    public bool $showActionMenu = false;
    public bool $showStatusModal = false;

    /**
     * Protected memory cache for the current request lifecycle.
     * Prevents duplicate DB queries without serializing sensitive data into wire:snapshot.
     */
    protected ?array $cachedScholarData = null;

    public function mount(): void
    {
        if (request()->has('open_scholar')) {
            $this->openDrawer((int) request()->get('open_scholar'));
        } elseif (session()->has('open_scholar_id')) {
            $this->openDrawer((int) session()->get('open_scholar_id'));
        }
    }

    public function render(): \Illuminate\View\View
    {
        $data = $this->loadScholar();

        return view('livewire.dashboard.scholar-drawer', [
            'scholarData' => $data['scholarData'],
            'file_groups' => $data['file_groups'],
        ]);
    }

    #[On("document-deleted")]
    public function onDocumentDeleted(): void
    {
        $this->cachedScholarData = null;
    }

    #[On('open-scholar-drawer')]
    public function openDrawer(int|string $scholarId, ?array $scholarData = null): void
    {
        $this->scholarId = (int) $scholarId;
        $this->isOpen = true;
        $this->cachedScholarData = null;
        $data = $this->loadScholar();
        $this->expandedFolders = array_keys($data['file_groups']);
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
        if ($this->scholarId) {
            $statusValue = in_array($newStatus, ['Clear', 'Cleared'], true) ? 'Cleared' : 'Not Cleared';
            $dbScholar = Scholar::find($this->scholarId);
            if ($dbScholar) {
                $statusModel = \App\Models\ClearanceStatus::where('name', $statusValue)->first();
                if ($statusModel) {
                    $dbScholar->clearance_status_id = $statusModel->id;
                    $dbScholar->save();
                    $this->cachedScholarData = null;
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

    public function openDocument(string $folderName, int $index): void
    {
        $data = $this->loadScholar();
        $folderFiles = $data['file_groups'][$folderName] ?? [];
        $document = $folderFiles[$index] ?? null;

        if (!$document) {
            return;
        }

        $uploadedAt = strtotime($document['uploaded_at'] ?? 'now');
        $secureStreamUrl = route('documents.view', ['document' => $document['uuid']]) . '?v=' . $uploadedAt;

        $this->dispatch('open-document-viewer',
            title: (string) ($document['file_type_name'] ?? $document['file_name'] ?? 'Document'),
            fileUrl: (string) $secureStreamUrl,
            scholarName: (string) ($data['scholarData']['name'] ?? 'Scholar'),
            fileType: str_contains($document['mime_type'] ?? '', 'image') ? 'image' : 'pdf',
            documentIndex: (int) $index,
            scholarId: (int) ($this->scholarId ?? -1),
            documentId: (string) ($document['uuid'] ?? ''),
        );
    }

    public function loadScholar(): array
    {
        if ($this->cachedScholarData !== null) {
            return $this->cachedScholarData;
        }

        if (!$this->scholarId) {
            return $this->cachedScholarData = [
                'scholarData' => null,
                'file_groups' => [],
            ];
        }

        $dbScholar = Scholar::with([
            'scholarship',
            'scholarshipType',
            'school',
            'course',
            'region',
            'province',
            'municipality',
            'barangay',
            'clearanceStatus',
        ])->find($this->scholarId);

        // dd($dbScholar);

        $files = DB::select("
            WITH RankedVersions AS (
                SELECT 
                    document_uuid,
                    file_type_id,
                    original_filename as file_name,
                    file_path,
                    file_size_bytes as file_size,
                    uploaded_by,
                    created_at as uploaded_at,
                    ROW_NUMBER() OVER(PARTITION BY document_uuid ORDER BY version_number DESC) as rn
                FROM document_versions
            )
            SELECT 
                d.uuid,
                d.documentable_id AS scholar_id,
                ft.name as file_type_name,
                rv.file_name,
                rv.file_path,
                rv.uploaded_at,
                u.name,
                rv.file_size,
                d.metadata
            FROM documents AS d
            INNER JOIN RankedVersions AS rv ON d.uuid = rv.document_uuid AND rv.rn = 1
            INNER JOIN file_types AS ft 
                ON rv.file_type_id = ft.id
            INNER JOIN users AS u 
                ON u.id = rv.uploaded_by
            WHERE d.documentable_type = :documentableType
                AND d.documentable_id = :scholarId
                AND d.deleted_at IS NULL
            ORDER BY ft.name ASC, rv.file_name ASC;
        ", [
            "documentableType" => "App\\Models\\Scholar", 
            "scholarId" => $this->scholarId
        ]);

        $filesArray = json_decode(json_encode($files), true);
        $file_groups = collect($filesArray)->groupBy('file_type_name')->toArray();

        if ($dbScholar) {
            $scholarData = [
                'id' => $dbScholar->id,
                'name' => "{$dbScholar->last_name}, {$dbScholar->first_name} {$dbScholar->middle_name}",
                'spas_id' => $dbScholar->spas_no ?? '—',
                'program' => $dbScholar->scholarship->name ?? ($dbScholar->scholarshipType->code ?? '—'),
                'program_type' => $dbScholar->scholarshipType->name ?? '—',
                'year_of_award' => $dbScholar->year_of_award ?? '—',
                'clearance_date' => $dbScholar->clearance_date ? \Carbon\Carbon::parse($dbScholar->clearance_date, 'Asia/Manila')->format('m / d / Y') : 'None (Not Cleared)',
                'course' => $dbScholar->course->name ?? '—',
                'university' => $dbScholar->school->name ? ($dbScholar->school->abbreviation ? ($dbScholar->school->name . ' (' . $dbScholar->school->abbreviation . ')') : $dbScholar->school->name) : '—',
                'address' => $dbScholar->barangay ? trim("{$dbScholar->barangay->name}") : '—',
                'municipality' => $dbScholar->municipality->name ?? '—',
                'province' => $dbScholar->province->name ?? '—',
                'region' => $dbScholar->region->name ?? '—',
                'email' => $dbScholar->email_address ?? '—',
                'contact' => $dbScholar->contact_number ?? '—',
                'birthdate' => $dbScholar->birthdate ? \Carbon\Carbon::parse($dbScholar->birthdate)->format('m / d / Y') : '—',
                'sex' => $dbScholar->sex ?? '—',
                'status' => $dbScholar->clearanceStatus->name ?? '—',
            ];
        } else {
            $scholarData = [
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
        }

        return $this->cachedScholarData = [
            'scholarData' => $scholarData,
            'file_groups' => $file_groups,
        ];
    }

    public function deleteScholar(){
        $scholar = Scholar::find($this->scholarId);
        
        if ($scholar){
            $scholar->delete();
        }
        $this->closeDrawer();
        $this->redirect(route('scholars.index'), navigate:true);
    }

    public function downloadScholar(): BinaryFileResponse
    {
        // Querying for the scholar and documents information
        $scholar = Scholar::find($this->scholarId);
        $groupedDocuments = $scholar->getScholarGroupedDocuments();
        
        // Prepares the ZIP File contents
        $zip = new ZipArchive();
        $scholarJSON = $scholar->getScholarProfileAsJSON();
        $scholarTXT = $scholar->getScholarProfileAsTXT();

        dd($scholar, $groupedDocuments, $scholarJSON, $scholarTXT, "Scholar was downloaded");
        return null;
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
                    <button type="button" class="btn btn-link text-muted p-1 border-0 shadow-none text-decoration-none" title="Download" wire:click="downloadScholar">
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
                                        <button type="button" class="dropdown-item-btn dropdown-item-btn--danger" wire:click="deleteScholar">
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
                        <label>Scholarship</label>
                        <value>{{ $scholarData['program'] }}</value>
                    </div>
                    <div class="scholar-meta-item">
                        <label>Scholarship Type</label>
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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="small text-muted fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Scanned Files</span>
                        <div class="folder-tab-actions">
                            <a 
                                href="{{ route('scholars.files.add', ['scholar' => $scholarId]) }}" 
                                wire:navigate 
                                class="btn-folder-action btn-folder-action--primary shadow-sm"
                            >
                                Add New Document
                            </a>
                            <button type="button" class="btn-folder-action btn-folder-action--secondary shadow-sm">
                                Sort
                            </button>
                        </div>
                    </div>

                    @foreach ($file_groups as $folderName => $items)
                        @php 
                            $isFolderExpanded = in_array($folderName, $expandedFolders, true); 
                        @endphp
                        
                        <div class="folder-accordion mb-2 {{ $isFolderExpanded ? 'folder-accordion--open' : '' }}">
                            <button wire:click="toggleFolder('{{ $folderName }}')" type="button" class="folder-tab-btn">
                                <span>{{ $folderName }}</span>
                            </button>

                            @if($isFolderExpanded)
                                <div class="folder-accordion__content">
                                    <div class="d-flex gap-3 overflow-auto pb-2">
                                        @foreach ($items as $index => $file)
                                            <div wire:click="openDocument('{{ $folderName }}', {{ $index }})" id="{{ $folderName }}" class="doc-thumbnail-card" role="button">
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
