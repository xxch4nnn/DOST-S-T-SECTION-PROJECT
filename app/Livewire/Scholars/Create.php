<?php

namespace App\Livewire\Scholars;

use App\Models\ClearanceStatus;
use App\Models\Course;
use App\Models\Region;
use App\Models\Scholar;
use App\Models\ScholarshipProgram;
use App\Models\ScholarshipProgramType;
use App\Models\School;
use Livewire\Component;

class Create extends Component
{
    public $first_name = '';

    public $middle_name = '';

    public $last_name = '';

    public $generational_suffix = '';

    public $year_of_award = '';

    public $scholarship_id = '';

    public $scholarship_type_id = '';

    public $spas_no = '';

    public $sex = '';

    public $birthdate = '';

    public $contact_number = '';

    public $email_address = '';

    public $school_id = '';

    public $course_id = '';

    public $program = '';

    public $barangay = '';

    public $municipality = '';

    public $district = '';

    public $province = '';

    public $region_id = '';

    public $clearance_status_id = 1; // Default

    public $clearance_date = '';

    public $for_disposal = false;

    public function save()
    {
        $validated = $this->validate([
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'generational_suffix' => 'nullable|string|max:5',
            'year_of_award' => 'required|integer',
            'scholarship_program_id' => 'required|exists:scholarship_programs,id',
            'scholarship_program_type_id' => 'required|exists:scholarship_program_types,id',
            'spas_number' => 'nullable|string|max:50',
            'sex' => 'nullable|string|in:Male,Female',
            'birthdate' => 'nullable|date',
            'contact_number' => 'nullable|string|max:11',
            'email_address' => 'nullable|email|max:70|unique:scholars,email_address',
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

        // Clean up empty optional fields
        foreach ($validated as $key => $value) {
            if ($value === '') {
                $validated[$key] = null;
            }
        }

        $scholar = Scholar::create($validated);

        return redirect()->route('scholars.show', $scholar->id);
    }

    public function render()
    {
        return view('livewire.scholars.create', [
            'scholarships' => ScholarshipProgram::orderBy('name', 'asc')->get(),
            'scholarshipTypes' => ScholarshipProgramType::orderBy('name', 'asc')->get(),
            'schools' => School::orderBy('name', 'asc')->get(),
            'courses' => Course::orderBy('name', 'asc')->get(),
            'regions' => Region::orderBy('name', 'asc')->get(),
            'clearanceStatuses' => ClearanceStatus::orderBy('name', 'asc')->get(),
        ])->layout('layouts.app');
    }
}
