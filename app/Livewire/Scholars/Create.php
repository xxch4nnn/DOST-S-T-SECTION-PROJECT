<?php

namespace App\Livewire\Scholars;

use App\Models\Barangay;
use App\Models\ClearanceStatus;
use App\Models\Course;
use App\Models\Municipality;
use App\Models\Province;
use App\Models\Region;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\ScholarshipType;
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
    
    public $region_id = '';
    public $province_id = '';
    public $municipality_id = '';
    public $barangay_id = '';

    
    public $province = '';
    
    public $home_address = ''; 


    public $clearance_status_id = ''; // Default

    public $clearance_date = '';

    public $for_disposal = false;

    public function save()
    {
        try {
            $this->sanitizeAll();

            $validated = $this->validate([
                'first_name' => 'required|string|max:50',
                'middle_name' => 'nullable|string|max:50',
                'last_name' => 'required|string|max:50',
                'generational_suffix' => 'nullable|string|max:5',
                'year_of_award' => 'required|integer',
                'scholarship_id' => 'required|exists:scholarships,id',
                'scholarship_type_id' => 'required|exists:scholarship_types,id',
                'spas_no' => 'nullable|string|max:50',
                'sex' => 'nullable|string|in:Male,Female',
                'birthdate' => 'nullable|date',
                'contact_number' => 'required|string|max:11',
                'email_address' => 'nullable|email|max:70|unique:scholars,email_address',
                'school_id' => 'required|exists:schools,id',
                'course_id' => 'required|exists:courses,id',
                // 'program' => 'nullable|string|max:150',
                'barangay' => 'nullable|string|max:150',
                'municipality' => 'nullable|string|max:150',
                // 'district' => 'nullable|string|max:150',
                'province' => 'nullable|string|max:150',
                'region_id' => 'required|exists:regions,id',
                'home_address' => 'nullable|string|max:200',
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

            return redirect()->route('scholars.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->validator->errors()->all());
        }
    }

    private function sanitizeAll(): void{
        $this->year_of_award = (int) $this->year_of_award;
    }

    // Code to add the live editing of files
    public function updatedRegionId($value): void
    {
        $this->updateProvince();
    }

    public function updatedProvinceId($value): void
    {
        $this->updateMunicipality();
    }

    public function updatedMunicipalityId($value): void
    {
        $this->updateBarangay();
    }

    public function updatedBarangayId($value): void
    {
        if ($this->barangay_id) {
            $b = Barangay::find($this->barangay_id);
            if ($b) {
                $this->barangay = $b->name;
            }
        }
    }

    public function updateProvince(): void
    {
        if (!$this->region_id) {
            $this->province_id = '';
            $this->province = '';
            $this->municipality_id = '';
            $this->municipality = '';
            $this->barangay_id = '';
            $this->barangay = '';
            return;
        }

        $availableProvinces = Province::where('region_id', $this->region_id)->get();
        if ($this->province_id && !$availableProvinces->contains('id', (int) $this->province_id)) {
            $this->province_id = '';
            $this->province = '';
        } elseif ($this->province_id) {
            $p = $availableProvinces->firstWhere('id', (int) $this->province_id);
            if ($p) {
                $this->province = $p->name;
            }
        }
        $this->updateMunicipality();
    }

    public function updateMunicipality(): void
    {
        if (!$this->province_id) {
            $this->municipality_id = '';
            $this->municipality = '';
            $this->barangay_id = '';
            $this->barangay = '';
            return;
        }

        $p = Province::find($this->province_id);
        if ($p) {
            $this->province = $p->name;
        }

        $availableMunicipalities = Municipality::where('province_id', $this->province_id)->get();
        if ($this->municipality_id && !$availableMunicipalities->contains('id', (int) $this->municipality_id)) {
            $this->municipality_id = '';
            $this->municipality = '';
        } elseif ($this->municipality_id) {
            $m = $availableMunicipalities->firstWhere('id', (int) $this->municipality_id);
            if ($m) {
                $this->municipality = $m->name;
            }
        }
        $this->updateBarangay();
    }

    public function updateBarangay(): void
    {
        if (!$this->municipality_id) {
            $this->barangay_id = '';
            $this->barangay = '';
            return;
        }

        $m = Municipality::find($this->municipality_id);
        if ($m) {
            $this->municipality = $m->name;
        }

        $availableBarangays = Barangay::where('municipality_id', $this->municipality_id)->get();
        if ($this->barangay_id && !$availableBarangays->contains('id', (int) $this->barangay_id)) {
            $this->barangay_id = '';
            $this->barangay = '';
        } elseif ($this->barangay_id) {
            $b = $availableBarangays->firstWhere('id', (int) $this->barangay_id);
            if ($b) {
                $this->barangay = $b->name;
            }
        }
    }

    public function render()
    {
        $provinces = $this->region_id
            ? Province::where('region_id', $this->region_id)->orderBy('name')->get()
            : collect();

        $municipalities = $this->province_id
            ? Municipality::where('province_id', $this->province_id)->orderBy('name')->get()
            : collect();

        $barangays = $this->municipality_id
            ? Barangay::where('municipality_id', $this->municipality_id)->orderBy('name')->get()
            : collect();

        return view('livewire.scholars.create', [
            'scholarships' => Scholarship::orderBy('name', 'asc')->get(),
            'scholarshipTypes' => ScholarshipType::orderBy('name', 'asc')->get(),
            'schools' => School::orderBy('name', 'asc')->get(),
            'courses' => Course::orderBy('name', 'asc')->get(),
            'regions' => Region::get(),
            'provinces' => $provinces,
            'municipalities' => $municipalities,
            'barangays' => $barangays,
            'clearanceStatuses' => ClearanceStatus::orderBy('name', 'asc')->get(),
        ])->layout('layouts.app');
    }
}
