<?php

namespace App\Livewire\Scholars;

use App\Models\ClearanceStatus;
use App\Models\Course;
use App\Models\Document;
use App\Models\FileType;
use App\Models\Region;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\ScholarshipType;
use App\Models\School;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    public $scholar;

    public $pendingUpload = null;

    public $pendingUploads = [];

    // Scholar form fields
    public string $first_name = '';

    public string $middle_name = '';

    public string $last_name = '';

    public string $generational_suffix = '';

    public string $year_of_award = '2023';

    public $scholarship_id = '';

    public $scholarship_type_id = '';

    public string $spas_no = '';

    public string $sex = 'Male';

    public string $birthdate = '';

    public string $contact_number = '';

    public string $email_address = '';

    public $school_id = '';

    public $course_id = '';

    public string $program = '';

    public string $barangay = '';

    public string $municipality = '';

    public string $district = '';

    public string $province = '';

    public $region_id = '';

    public $clearance_status_id = '';

    public string $clearance_date = '';

    public bool $for_disposal = false;

    // Document categories & staged management
    public array $scannedCategories = [];

    public array $deletedDocumentIds = [];

    public function mount(int|string|Scholar $scholar): void
    {
        if ($scholar instanceof Scholar) {
            $this->scholar = $scholar;
        } else {
            $this->scholar = Scholar::findOrFail($scholar);
        }

        $array = $this->scholar->toArray();
        foreach ($array as $key => $value) {
            $array[$key] = $value ?? '';
        }
        // dd($array);

        $this->fill($array);

        // Format dates
        if ($this->birthdate && $this->scholar->birthdate) {
            $this->birthdate = is_string($this->scholar->birthdate) ? substr($this->scholar->birthdate, 0, 10) : $this->scholar->birthdate->format('Y-m-d');
        }
        if ($this->clearance_date && $this->scholar->clearance_date) {
            $this->clearance_date = is_string($this->scholar->clearance_date) ? substr($this->scholar->clearance_date, 0, 10) : $this->scholar->clearance_date->format('Y-m-d');
        }

        // Load existing documents grouped by category if model exists
        $documents = $this->scholar->exists
            ? $this->scholar->documents()->with(['currentVersion.fileType'])->get()
            : collect();

        if ($documents->isEmpty()) {
            $defaultType = FileType::first();
            $defaultName = $defaultType ? $defaultType->name : 'Scholarship Agreement';
            $this->scannedCategories = [
                [
                    'id' => 'cat_1',
                    'name' => $defaultName,
                    'selected_type' => $defaultName,
                    'is_custom' => false,
                    'files' => [],
                ],
            ];
        } else {
            $grouped = $documents->groupBy(function ($doc) {
                return $doc->fileType ? $doc->fileType->name : 'General Documents';
            });

            $categories = [];
            $catNum = 1;
            foreach ($grouped as $catName => $docGroup) {
                $catId = 'cat_'.$catNum++;
                $files = [];

                foreach ($docGroup as $doc) {
                    $originalName = $doc->original_filename;
                    $mimeType = $doc->mime_type ?: 'application/pdf';
                    $isPdf = str_contains(strtolower($mimeType), 'pdf') || str_ends_with(strtolower($originalName), '.pdf');
                    $isImage = str_starts_with($mimeType, 'image/') || in_array(strtolower(pathinfo($originalName, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'gif']);

                    $files[] = [
                        'id' => $doc->uuid,
                        'name' => $originalName,
                        'size' => ($doc->file_size_kb ?: 1) * 1024,
                        'mime_type' => $mimeType,
                        'is_pdf' => $isPdf,
                        'is_image' => $isImage,
                        'is_existing' => true,
                        'download_url' => route('documents.download', ['document' => $doc->uuid]),
                        'url' => route('documents.view', ['document' => $doc->uuid]),
                        'thumbnail_url' => $isImage ? route('documents.download', ['document' => $doc->uuid]) : null,
                    ];
                }

                $categories[] = [
                    'id' => $catId,
                    'name' => $catName,
                    'selected_type' => $catName,
                    'is_custom' => false,
                    'files' => $files,
                ];
            }

            $this->scannedCategories = $categories;
        }
    }

    public function addScannedCategory(): void
    {
        $newIndex = count($this->scannedCategories) + 1;
        $this->scannedCategories[] = [
            'id' => 'cat_'.uniqid(),
            'name' => 'Additional Documents '.$newIndex,
            'selected_type' => 'custom',
            'is_custom' => true,
            'files' => [],
        ];
    }

    public function removeScannedCategory(int $index): void
    {
        if (isset($this->scannedCategories[$index])) {
            foreach ($this->scannedCategories[$index]['files'] as $file) {
                if (! empty($file['is_existing']) && ! empty($file['id'])) {
                    $doc = Document::where('id', $file['id'])
                        ->where('documentable_type', Scholar::class)
                        ->where('documentable_id', $this->scholar->id)
                        ->first();
                    if ($doc) {
                        $this->authorize('delete', $doc);
                        $this->deletedDocumentIds[] = $file['id'];
                    }
                }
            }
            array_splice($this->scannedCategories, $index, 1);
            $this->scannedCategories = array_values($this->scannedCategories);
        }
    }

    public function setCategoryType(int $index, string $typeName): void
    {
        if (isset($this->scannedCategories[$index])) {
            $this->scannedCategories[$index]['selected_type'] = $typeName;
            if ($typeName !== 'custom') {
                $this->scannedCategories[$index]['name'] = $typeName;
                $this->scannedCategories[$index]['is_custom'] = false;
            } else {
                $this->scannedCategories[$index]['is_custom'] = true;
            }
        }
    }

    public function deleteExistingDocument(int $docId): void
    {
        $doc = Document::where('id', $docId)
            ->where('documentable_type', Scholar::class)
            ->where('documentable_id', $this->scholar->id)
            ->firstOrFail();

        $this->authorize('delete', $doc);

        $this->deletedDocumentIds[] = $docId;
        foreach ($this->scannedCategories as &$cat) {
            $cat['files'] = array_values(array_filter($cat['files'], function ($f) use ($docId) {
                return ($f['id'] ?? null) != $docId;
            }));
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
                foreach ($uploads as $uploaded) {
                    if (! $uploaded) {
                        continue;
                    }

                    $originalName = $uploaded->getClientOriginalName();
                    $fileSize = $uploaded->getSize();
                    $mimeType = $uploaded->getMimeType() ?: 'application/octet-stream';
                    $tempPath = $uploaded->store('temp_documents', 'local');

                    $isPdf = str_contains(strtolower($mimeType), 'pdf') || str_ends_with(strtolower($originalName), '.pdf');
                    $isImage = str_starts_with($mimeType, 'image/');

                    $thumbnailUrl = $thumbnails[$originalName] ?? null;

                    $this->scannedCategories[$idx]['files'][] = [
                        'name' => $originalName,
                        'size' => $fileSize,
                        'mime_type' => $mimeType,
                        'temp_path' => $tempPath,
                        'is_pdf' => $isPdf,
                        'is_image' => $isImage,
                        'is_existing' => false,
                        'thumbnail_url' => $thumbnailUrl,
                    ];
                }
                break;
            }
        }

        $this->pendingUpload = null;
        $this->pendingUploads = [];
    }

    public function saveScholarWithStagedFiles(array $manifest = [])
    {
        $validated = $this->validate([
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'generational_suffix' => 'nullable|string|max:5',
            'year_of_award' => 'required|integer',
            'scholarship_id' => 'required|exists:scholarships,id',
            'scholarship_type_id' => 'required|exists:scholarship_types,id',
            'spas_no' => 'required|string|max:50',
            'sex' => 'nullable|string|in:Male,Female',
            'birthdate' => 'nullable|date',
            'contact_number' => 'nullable|string|max:11',
            'email_address' => 'nullable|email|max:70|unique:scholars,email_address,'.($this->scholar->id ?? 'NULL'),
            'school_id' => 'required|exists:schools,id',
            'course_id' => 'nullable|exists:courses,id',
            'program' => 'nullable|string|max:150',
            'barangay' => 'nullable|string|max:150',
            'municipality' => 'nullable|string|max:150',
            'district' => 'nullable|string|max:150',
            'province' => 'nullable|string|max:150',
            'region_id' => 'required|exists:regions,id',
            'clearance_status_id' => 'required|exists:clearance_statuses,id',
            'clearance_date' => 'nullable|date',
            'for_disposal' => 'boolean',
        ]);

        if ($this->scholar->exists) {
            $this->scholar->update($validated);
        } else {
            $this->scholar = Scholar::create($validated);
        }

        // Delete any marked existing documents
        if (! empty($this->deletedDocumentIds)) {
            $toDelete = Document::whereIn('id', $this->deletedDocumentIds)
                ->where('documentable_type', Scholar::class)
                ->where('documentable_id', $this->scholar->id)
                ->get();

            foreach ($toDelete as $doc) {
                $this->authorize('delete', $doc);
                foreach ($doc->versions as $version) {
                    if ($version->file_path) {
                        Storage::disk('local')->delete($version->file_path);
                    } elseif ($version->stored_filename) {
                        Storage::disk('local')->delete('documents/'.$version->stored_filename);
                    }
                }
                $doc->delete();
            }
        }

        // Process newly staged files from manifest
        if (! empty($manifest)) {
            $uploadedMap = [];
            if (! empty($this->pendingUploads)) {
                foreach ($this->pendingUploads as $upload) {
                    if (is_object($upload) && method_exists($upload, 'getClientOriginalName')) {
                        $uploadedMap[$upload->getClientOriginalName()] = $upload;
                    }
                }
            }

            foreach ($manifest as $idx => $item) {
                if (! empty($item['is_existing'])) {
                    continue;
                }

                $categoryName = trim($item['cat_name'] ?? '') ?: 'General Documents';
                $originalName = $item['name'] ?? 'Document';
                $fileSizeKb = max(1, (int) round(($item['file_size'] ?? 1024) / 1024));
                $mimeType = $item['mime_type'] ?? 'application/pdf';

                $fileType = FileType::firstOrCreate(['name' => $categoryName]);

                $uploaded = $uploadedMap[$originalName] ?? ($this->pendingUploads[$idx] ?? null);

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

                Document::createWithInitialVersion(
                    [
                        'documentable_type' => Scholar::class,
                        'documentable_id' => $this->scholar->id,
                        'status' => 'active',
                        'metadata' => ['category' => $categoryName],
                    ],
                    [
                        'file_type_id' => $fileType->id,
                        'original_filename' => $originalName,
                        'stored_filename' => $storedFilename,
                        'file_path' => $targetPath,
                        'mime_type' => $mimeType,
                        'file_size_kb' => $fileSizeKb,
                        'uploaded_by' => auth()->id(),
                    ]
                );
            }
        } else {
            // Manifest empty: persist temp files staged via processPendingUploads only (avoid double insert).
            foreach ($this->scannedCategories as $cat) {
                $categoryName = trim($cat['name'] ?? '') ?: 'General Documents';
                if (empty($cat['files'])) {
                    continue;
                }

                $fileType = FileType::firstOrCreate(['name' => $categoryName]);

                foreach ($cat['files'] as $fileMeta) {
                    if (! empty($fileMeta['is_existing'])) {
                        continue;
                    }

                    $tempPath = $fileMeta['temp_path'] ?? null;
                    if (! $tempPath || ! Storage::disk('local')->exists($tempPath)) {
                        throw ValidationException::withMessages([
                            'scanned_files' => 'Uploaded file payload missing for '.($fileMeta['name'] ?? 'Document').'.',
                        ]);
                    }

                    $originalName = $fileMeta['name'] ?? 'Document';
                    $mimeType = $fileMeta['mime_type'] ?? 'application/pdf';
                    $fileSizeKb = max(1, (int) round(($fileMeta['size'] ?? 1024) / 1024));

                    $extension = pathinfo($originalName, PATHINFO_EXTENSION) ?: (! empty($fileMeta['is_pdf']) ? 'pdf' : 'png');
                    $storedFilename = 'doc_'.uniqid().'.'.$extension;
                    $targetPath = 'documents/'.$storedFilename;

                    Storage::disk('local')->move($tempPath, $targetPath);

                    Document::createWithInitialVersion(
                        [
                            'documentable_type' => Scholar::class,
                            'documentable_id' => $this->scholar->id,
                            'status' => 'active',
                            'metadata' => ['category' => $categoryName],
                        ],
                        [
                            'file_type_id' => $fileType->id,
                            'original_filename' => $originalName,
                            'stored_filename' => $storedFilename,
                            'file_path' => $targetPath,
                            'mime_type' => $mimeType,
                            'file_size_kb' => $fileSizeKb,
                            'uploaded_by' => auth()->id(),
                        ]
                    );
                }
            }
        }

        session()->flash('status', 'Scholar updated successfully.');

        return $this->redirect(route('scholars.index', ['open_scholar' => $this->scholar->id]), navigate: true);
    }

    public function save()
    {
        return $this->saveScholarWithStagedFiles([]);
    }

    public function render()
    {
        return view('livewire.scholars.edit', [
            'scholarships' => Scholarship::all(),
            'scholarshipTypes' => ScholarshipType::all(),
            'schools' => School::orderBy('name')->get(),
            'courses' => Course::orderBy('name')->get(),
            'regions' => Region::orderBy('name')->get(),
            'clearanceStatuses' => ClearanceStatus::all(),
            'availableFileTypes' => FileType::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
