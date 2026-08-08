<?php

namespace App\Livewire;

use App\Models\ClearanceStatus;
use App\Models\Course;
use App\Models\Document;
use App\Models\FileType;
use App\Models\Region;
use App\Models\Scholar;
use App\Models\ScholarshipProgram;
use App\Models\ScholarshipProgramType;
use App\Models\School;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddFile extends Component
{
    use WithFileUploads;

    public $pendingUpload = null;

    public $pendingUploads = [];

    // Wizard step state
    public ?string $fileType = null; // 'scholar' | 'admin'

    public ?string $adminCategory = null; // 'Memorandum', 'Annual Financial Reports', etc.

    // Scholar form fields
    public string $last_name = '';

    public string $first_name = '';

    public string $middle_name = '';

    public string $generational_suffix = '';

    public string $spas_no = '';

    public string $year_of_award = '2023';

    public $scholarship_id = '';

    public $scholarship_type_id = '';

    public $school_id = '';

    public $course_id = '';

    public $clearance_status_id = '';

    public string $clearance_date = '';

    public string $barangay = '';

    public string $municipality = '';

    public string $province = '';

    public $region_id = '';

    public string $birthdate = '';

    public string $sex = 'Male';

    // Admin record fields
    public string $admin_title = '';

    public string $admin_series_number = '';

    public string $admin_year = '2023';

    public string $admin_recipient = '';

    public string $admin_description = '';

    public array $adminCategories = [
        'Memorandum',
        'Annual Financial Reports',
        'Quarterly Financial Reports',
        'Payrolls',
        'Endorsements',
        'Communications',
    ];

    // Dynamic Scanned File Categories with Dropdown & Rename Support
    public array $scannedCategories = [
        [
            'id' => 'cat_1',
            'name' => 'Amendatory Agreement',
            'selected_type' => 'Amendatory Agreement',
            'is_custom' => false,
            'files' => [],
        ],
    ];

    public function mount(): void
    {
        // Preload default IDs if available
        $defaultScholarship = Scholarship::first();
        if ($defaultScholarship) {
            $this->scholarship_id = $defaultScholarship->id;
        }

        $defaultType = ScholarshipType::first();
        if ($defaultType) {
            $this->scholarship_type_id = $defaultType->id;
        }

        $defaultStatus = ClearanceStatus::first();
        if ($defaultStatus) {
            $this->clearance_status_id = $defaultStatus->id;
        }
    }

    public function processPendingUploads(string $catId, array $thumbnails = []): void
    {
        $uploads = is_array($this->pendingUploads) ? $this->pendingUploads : [];
        if ($this->pendingUpload && empty($uploads)) {
            $uploads = [$this->pendingUpload];
        }

        foreach ($this->scannedCategories as $idx => $cat) {
            if ($cat['id'] === $catId) {
                foreach ($uploads as $uIdx => $uploaded) {
                    if (! $uploaded) {
                        continue;
                    }

                    $originalName = $uploaded->getClientOriginalName();
                    $fileSize = $uploaded->getSize();
                    $mimeType = $uploaded->getMimeType() ?: 'application/octet-stream';
                    $tempPath = $uploaded->store('temp_documents', 'local');

                    $isPdf = str_contains(strtolower($mimeType), 'pdf') || str_ends_with(strtolower($originalName), '.pdf');
                    $isImage = str_starts_with($mimeType, 'image/');

                    if ($fileSize >= 1048576) {
                        $sizeFormatted = round($fileSize / 1048576, 1).' MB';
                    } else {
                        $sizeFormatted = max(1, round($fileSize / 1024)).' KB';
                    }

                    $thumbnail = $thumbnails[$uIdx] ?? ($thumbnails[$originalName] ?? null);

                    $this->scannedCategories[$idx]['files'][] = [
                        'id' => 'file_'.uniqid().'_'.bin2hex(random_bytes(3)),
                        'name' => $originalName,
                        'size' => $fileSize,
                        'size_formatted' => $sizeFormatted,
                        'mime_type' => $mimeType,
                        'is_pdf' => $isPdf,
                        'is_image' => $isImage,
                        'temp_path' => $tempPath,
                        'thumbnail_url' => $thumbnail,
                    ];
                }
                break;
            }
        }

        $this->pendingUploads = [];
        $this->pendingUpload = null;
    }

    public function storeTempUpload(string $catId, string $fileId, string $originalName, int $fileSize, string $mimeType, ?string $thumbnail = null): void
    {
        $tempPath = null;
        if ($this->pendingUpload) {
            $tempPath = $this->pendingUpload->store('temp_documents', 'local');
            $this->pendingUpload = null;
        }

        foreach ($this->scannedCategories as $idx => $cat) {
            if ($cat['id'] === $catId) {
                $isPdf = str_contains(strtolower($mimeType), 'pdf') || str_ends_with(strtolower($originalName), '.pdf');
                $isImage = str_starts_with($mimeType, 'image/');

                if ($fileSize >= 1048576) {
                    $sizeFormatted = round($fileSize / 1048576, 1).' MB';
                } else {
                    $sizeFormatted = max(1, round($fileSize / 1024)).' KB';
                }

                $this->scannedCategories[$idx]['files'][] = [
                    'id' => $fileId,
                    'name' => $originalName,
                    'size' => $fileSize,
                    'size_formatted' => $sizeFormatted,
                    'mime_type' => $mimeType,
                    'is_pdf' => $isPdf,
                    'is_image' => $isImage,
                    'temp_path' => $tempPath,
                    'thumbnail_url' => $thumbnail,
                ];
                break;
            }
        }
    }

    public function addFileToCategory(string $catId, array $fileData): void
    {
        foreach ($this->scannedCategories as $idx => $cat) {
            if ($cat['id'] === $catId) {
                $fileId = $fileData['id'] ?? ('file_'.uniqid());
                $name = $fileData['name'] ?? 'Document';
                $size = (int) ($fileData['size'] ?? 1024);
                $mimeType = $fileData['mime_type'] ?? 'application/pdf';
                $isPdf = (bool) ($fileData['is_pdf'] ?? false);
                $isImage = (bool) ($fileData['is_image'] ?? false);

                // Format size
                if ($size >= 1048576) {
                    $sizeFormatted = round($size / 1048576, 1).' MB';
                } else {
                    $sizeFormatted = max(1, round($size / 1024)).' KB';
                }

                $this->scannedCategories[$idx]['files'][] = [
                    'id' => $fileId,
                    'name' => $name,
                    'size' => $size,
                    'size_formatted' => $fileData['size_formatted'] ?? $sizeFormatted,
                    'mime_type' => $mimeType,
                    'is_pdf' => $isPdf,
                    'is_image' => $isImage,
                    'temp_path' => $fileData['temp_path'] ?? null,
                    'data_url' => $fileData['data_url'] ?? null,
                    'thumbnail_url' => $fileData['thumbnail_url'] ?? ($fileData['data_url'] ?? null),
                ];
                break;
            }
        }
    }

    public function addMultipleFilesToCategory(string $catId, array $filesData): void
    {
        foreach ($filesData as $fileData) {
            $this->addFileToCategory($catId, $fileData);
        }
    }

    public function removeFileById(string $catId, string $fileId): void
    {
        foreach ($this->scannedCategories as $idx => $cat) {
            if ($cat['id'] === $catId) {
                foreach ($cat['files'] as $f) {
                    if (($f['id'] ?? '') === $fileId && ! empty($f['temp_path']) && Storage::disk('local')->exists($f['temp_path'])) {
                        Storage::disk('local')->delete($f['temp_path']);
                    }
                }
                $this->scannedCategories[$idx]['files'] = array_values(
                    array_filter($cat['files'], fn ($f) => ($f['id'] ?? '') !== $fileId)
                );
                break;
            }
        }
    }

    public function addScannedCategory(): void
    {
        $count = count($this->scannedCategories) + 1;
        $uniqueId = 'cat_'.uniqid();

        // Pick next suggested type from standard DOST categories
        $standardTypes = [
            'Amendatory Agreement',
            'Report of Grades',
            'Certificate of Grades (COG)',
            'Transcript of Records (TOR)',
            'Scholarship Agreement',
            'Certificate of Graduation / Diploma',
            'Enrollment / Registration Form',
            'Clearance Form / Certificate',
            'Official Receipt (O.R.)',
            'Medical Certificate',
            'Other Supporting Documents',
        ];

        $usedNames = array_column($this->scannedCategories, 'name');
        $suggestedName = "Scanned Files #{$count}";
        foreach ($standardTypes as $type) {
            if (! in_array($type, $usedNames, true)) {
                $suggestedName = $type;
                break;
            }
        }

        $this->scannedCategories[] = [
            'id' => $uniqueId,
            'name' => $suggestedName,
            'selected_type' => in_array($suggestedName, $standardTypes, true) ? $suggestedName : 'custom',
            'is_custom' => ! in_array($suggestedName, $standardTypes, true),
            'files' => [],
        ];
    }

    public function removeScannedCategory(int $index): void
    {
        if (isset($this->scannedCategories[$index]) && count($this->scannedCategories) > 1) {
            unset($this->scannedCategories[$index]);
            $this->scannedCategories = array_values($this->scannedCategories);
        }
    }

    public function setCategoryType(int $index, string $type): void
    {
        if (isset($this->scannedCategories[$index])) {
            $this->scannedCategories[$index]['selected_type'] = $type;
            if ($type !== 'custom') {
                $this->scannedCategories[$index]['name'] = $type;
                $this->scannedCategories[$index]['is_custom'] = false;
            } else {
                $this->scannedCategories[$index]['is_custom'] = true;
            }
        }
    }

    public function removeFileFromCategory(int $catIndex, int $fileIndex): void
    {
        if (isset($this->scannedCategories[$catIndex]['files'][$fileIndex])) {
            unset($this->scannedCategories[$catIndex]['files'][$fileIndex]);
            $this->scannedCategories[$catIndex]['files'] = array_values($this->scannedCategories[$catIndex]['files']);
        }
    }

    public function reorderFiles(string $catId, array $orderedIds): void
    {
        foreach ($this->scannedCategories as $idx => $cat) {
            if ($cat['id'] === $catId) {
                $currentFiles = collect($cat['files'])->keyBy('id');
                $newOrderedFiles = [];
                foreach ($orderedIds as $id) {
                    if ($currentFiles->has($id)) {
                        $newOrderedFiles[] = $currentFiles->get($id);
                    }
                }
                $this->scannedCategories[$idx]['files'] = $newOrderedFiles;
                break;
            }
        }
    }

    public function discard(): void
    {
        $this->fileType = null;
        $this->adminCategory = null;
        $this->reset(['last_name', 'first_name', 'middle_name', 'generational_suffix', 'spas_no', 'barangay', 'municipality', 'province']);
        $this->scannedCategories = [
            [
                'id' => 'cat_1',
                'name' => 'Amendatory Agreement',
                'selected_type' => 'Amendatory Agreement',
                'is_custom' => false,
                'files' => [],
            ],
        ];
    }

    public function selectFileType(?string $type): void
    {
        $this->fileType = $type;
        $this->adminCategory = null;
    }

    public function selectAdminCategory(?string $category): void
    {
        $this->adminCategory = $category;
    }

    public function goBack(): void
    {
        if ($this->fileType === 'admin' && $this->adminCategory !== null) {
            $this->adminCategory = null;
        } else {
            $this->fileType = null;
            $this->adminCategory = null;
        }
    }

    public function saveScholarWithStagedFiles(array $manifest = [])
    {
        $validated = $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'generational_suffix' => 'nullable|string|max:255',
            'spas_no' => 'required|string|max:255|unique:scholars,spas_no',
            'year_of_award' => 'required|integer',
            'scholarship_id' => 'nullable|exists:scholarships,id',
            'scholarship_type_id' => 'nullable|exists:scholarship_types,id',
            'school_id' => 'nullable|exists:schools,id',
            'course_id' => 'nullable|exists:courses,id',
            'clearance_status_id' => 'nullable|exists:clearance_statuses,id',
            'clearance_date' => 'nullable|date',
            'barangay' => 'nullable|string|max:255',
            'municipality' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'region_id' => 'nullable|exists:regions,id',
            'birthdate' => 'nullable|date',
            'sex' => 'nullable|string|max:10',
        ]);

        $scholar = Scholar::create($validated);

        if (! Storage::disk('local')->exists('documents')) {
            Storage::disk('local')->makeDirectory('documents');
        }

        $uploadedCount = 0;
        $uploads = is_array($this->pendingUploads) ? $this->pendingUploads : [];

        if (! empty($manifest)) {
            foreach ($manifest as $mIdx => $item) {
                $catName = trim($item['cat_name'] ?? '') ?: 'General Documents';
                $fileType = FileType::firstOrCreate(['name' => $catName]);

                $uploadIdx = $item['index'] ?? $mIdx;
                $uploaded = $uploads[$uploadIdx] ?? null;

                $originalName = $item['name'] ?? ($uploaded && method_exists($uploaded, 'getClientOriginalName') ? $uploaded->getClientOriginalName() : 'Document');
                $mimeType = $item['mime_type'] ?? ($uploaded && method_exists($uploaded, 'getMimeType') ? $uploaded->getMimeType() : 'application/pdf');
                $fileSize = (int) ($item['file_size'] ?? ($uploaded && method_exists($uploaded, 'getSize') ? $uploaded->getSize() : 1024));
                $fileSizeKb = max(1, (int) round($fileSize / 1024));

                $extension = pathinfo($originalName, PATHINFO_EXTENSION) ?: (! empty($item['is_pdf']) ? 'pdf' : 'png');
                $storedFilename = 'doc_'.uniqid().'.'.$extension;
                $targetPath = 'documents/'.$storedFilename;

                if ($uploaded && is_object($uploaded) && method_exists($uploaded, 'storeAs')) {
                    $uploaded->storeAs('documents', $storedFilename, 'local');
                } else {
                    throw ValidationException::withMessages([
                        'scanned_files' => "Uploaded file payload missing for {$originalName}.",
                    ]);
                }

                $doc = Document::create([
                    'documentable_type' => Scholar::class,
                    'documentable_id' => $scholar->id,
                    'file_type_id' => $fileType->id,
                    'original_filename' => $originalName,
                    'stored_filename' => $storedFilename,
                    'mime_type' => $mimeType,
                    'file_size_kb' => $fileSizeKb,
                    'status' => 'active',
                    'uploaded_by' => auth()->id(),
                ]);

                $doc->versions()->create([
                    'stored_filename' => $storedFilename,
                    'original_filename' => $originalName,
                    'file_size_kb' => $fileSizeKb,
                    'version_number' => 1,
                    'replaced_by_user_id' => auth()->id(),
                ]);

                $uploadedCount++;
            }
        } else {
            foreach ($this->scannedCategories as $cat) {
                $categoryName = trim($cat['name'] ?? '') ?: 'General Documents';
                if (! empty($cat['files'])) {
                    $fileType = FileType::firstOrCreate(['name' => $categoryName]);

                    foreach ($cat['files'] as $fileMeta) {
                        $originalName = $fileMeta['name'] ?? 'Document';
                        $mimeType = $fileMeta['mime_type'] ?? 'application/pdf';
                        $fileSizeKb = max(1, (int) round(($fileMeta['size'] ?? 1024) / 1024));
                        $dataUrl = $fileMeta['data_url'] ?? null;
                        $tempPath = $fileMeta['temp_path'] ?? null;

                        $extension = pathinfo($originalName, PATHINFO_EXTENSION) ?: (! empty($fileMeta['is_pdf']) ? 'pdf' : 'png');
                        $storedFilename = 'doc_'.uniqid().'.'.$extension;
                        $targetPath = 'documents/'.$storedFilename;

                        if ($tempPath && Storage::disk('local')->exists($tempPath)) {
                            Storage::disk('local')->move($tempPath, $targetPath);
                        } elseif ($tempPath && file_exists($tempPath)) {
                            Storage::disk('local')->put($targetPath, file_get_contents($tempPath));
                        } elseif ($dataUrl && str_contains($dataUrl, 'base64,')) {
                            $binaryContent = base64_decode(explode('base64,', $dataUrl)[1]);
                            Storage::disk('local')->put($targetPath, $binaryContent);
                        } else {
                            throw ValidationException::withMessages([
                                'scanned_files' => "Uploaded file payload missing for {$originalName}.",
                            ]);
                        }

                        $doc = Document::create([
                            'documentable_type' => Scholar::class,
                            'documentable_id' => $scholar->id,
                            'file_type_id' => $fileType->id,
                            'original_filename' => $originalName,
                            'stored_filename' => $storedFilename,
                            'mime_type' => $mimeType,
                            'file_size_kb' => $fileSizeKb,
                            'status' => 'active',
                            'uploaded_by' => auth()->id(),
                        ]);

                        $doc->versions()->create([
                            'stored_filename' => $storedFilename,
                            'original_filename' => $originalName,
                            'file_size_kb' => $fileSizeKb,
                            'version_number' => 1,
                            'replaced_by_user_id' => auth()->id(),
                        ]);

                        $uploadedCount++;
                    }
                }
            }
        }

        $this->pendingUploads = [];
        $this->pendingUpload = null;

        session()->flash('status', "Scholar file and {$uploadedCount} scanned document(s) registered successfully!");
        session()->flash('open_scholar_id', $scholar->id);

        return redirect()->route('scholars.index', ['open_scholar' => $scholar->id]);
    }

    public function saveScholar()
    {
        $validated = $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'generational_suffix' => 'nullable|string|max:255',
            'spas_no' => 'required|string|max:255|unique:scholars,spas_no',
            'year_of_award' => 'required|integer',
            'scholarship_id' => 'nullable|exists:scholarships,id',
            'scholarship_type_id' => 'nullable|exists:scholarship_types,id',
            'school_id' => 'nullable|exists:schools,id',
            'course_id' => 'nullable|exists:courses,id',
            'clearance_status_id' => 'nullable|exists:clearance_statuses,id',
            'clearance_date' => 'nullable|date',
            'barangay' => 'nullable|string|max:255',
            'municipality' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'region_id' => 'nullable|exists:regions,id',
            'birthdate' => 'nullable|date',
            'sex' => 'nullable|string|max:10',
        ]);

        $scholar = Scholar::create($validated);

        // Ensure storage directory exists
        if (! Storage::disk('local')->exists('documents')) {
            Storage::disk('local')->makeDirectory('documents');
        }

        // Process Scanned Categories & Files
        $uploadedCount = 0;
        foreach ($this->scannedCategories as $cat) {
            $categoryName = trim($cat['name'] ?? '') ?: 'General Documents';

            // Find or create FileType
            $fileType = FileType::firstOrCreate(['name' => $categoryName]);

            if (! empty($cat['files'])) {
                foreach ($cat['files'] as $fileMeta) {
                    $originalName = $fileMeta['name'] ?? 'Document';
                    $mimeType = $fileMeta['mime_type'] ?? 'application/pdf';
                    $fileSizeKb = max(1, (int) round(($fileMeta['size'] ?? 1024) / 1024));
                    $dataUrl = $fileMeta['data_url'] ?? null;
                    $tempPath = $fileMeta['temp_path'] ?? null;

                    // Generate safe stored filename
                    $extension = pathinfo($originalName, PATHINFO_EXTENSION) ?: (! empty($fileMeta['is_pdf']) ? 'pdf' : 'png');
                    $storedFilename = 'doc_'.uniqid().'.'.$extension;
                    $targetPath = 'documents/'.$storedFilename;

                    if ($tempPath && Storage::disk('local')->exists($tempPath)) {
                        Storage::disk('local')->move($tempPath, $targetPath);
                    } elseif ($tempPath && file_exists($tempPath)) {
                        Storage::disk('local')->put($targetPath, file_get_contents($tempPath));
                    } elseif ($dataUrl && str_contains($dataUrl, 'base64,')) {
                        $binaryContent = base64_decode(explode('base64,', $dataUrl)[1]);
                        Storage::disk('local')->put($targetPath, $binaryContent);
                    } else {
                        throw ValidationException::withMessages([
                            'scanned_files' => "Uploaded file payload missing for {$originalName}.",
                        ]);
                    }

                    $doc = Document::create([
                        'documentable_type' => Scholar::class,
                        'documentable_id' => $scholar->id,
                        'file_type_id' => $fileType->id,
                        'original_filename' => $originalName,
                        'stored_filename' => $storedFilename,
                        'mime_type' => $mimeType,
                        'file_size_kb' => $fileSizeKb,
                        'status' => 'active',
                        'uploaded_by' => auth()->id(),
                    ]);

                    $doc->versions()->create([
                        'stored_filename' => $storedFilename,
                        'original_filename' => $originalName,
                        'file_size_kb' => $fileSizeKb,
                        'version_number' => 1,
                        'replaced_by_user_id' => auth()->id(),
                    ]);

                    $uploadedCount++;
                }
            }
        }

        session()->flash('status', "Scholar file and {$uploadedCount} scanned document(s) registered successfully!");
        session()->flash('open_scholar_id', $scholar->id);

        return redirect()->route('scholars.index', ['open_scholar' => $scholar->id]);
    }

    public function saveAdminRecord()
    {
        $validated = $this->validate([
            'admin_title' => 'required|string|max:255',
            'admin_series_number' => 'nullable|string|max:255',
            'admin_year' => 'required|integer',
            'admin_recipient' => 'nullable|string|max:255',
            'admin_description' => 'nullable|string',
        ]);

        AdministrativeRecord::create([
            'title' => $this->admin_title,
            'record_type' => $this->adminCategory ?? 'Memorandum',
            'series_number' => $this->admin_series_number,
            'year' => (int) $this->admin_year,
            'recipient' => $this->admin_recipient,
            'description' => $this->admin_description,
            'created_by' => auth()->id(),
        ]);

        session()->flash('status', 'Administrative record saved successfully!');

        return redirect()->route('admin-records.index');
    }

    public function render()
    {
        return view('livewire.add-file', [
            'scholarships' => ScholarshipProgram::orderBy('name')->get(),
            'scholarshipTypes' => ScholarshipProgramType::orderBy('name')->get(),
            'schools' => School::orderBy('name')->get(),
            'courses' => Course::orderBy('name')->get(),
            'clearanceStatuses' => ClearanceStatus::orderBy('name')->get(),
            'regions' => Region::get(),
        ])->layout('layouts.app');
    }
}
