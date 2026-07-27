<?php

namespace App\Livewire;

use App\Models\AdministrativeRecord;
use App\Models\ClearanceStatus;
use App\Models\Course;
use App\Models\Region;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\ScholarshipType;
use App\Models\School;
use Livewire\Component;

class AddFile extends Component
{
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

        session()->flash('status', 'Scholar file registered successfully!');

        return redirect()->route('scholars.index');
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
            'scholarships' => Scholarship::orderBy('name')->get(),
            'scholarshipTypes' => ScholarshipType::orderBy('name')->get(),
            'schools' => School::orderBy('name')->get(),
            'courses' => Course::orderBy('name')->get(),
            'clearanceStatuses' => ClearanceStatus::orderBy('name')->get(),
            'regions' => Region::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
