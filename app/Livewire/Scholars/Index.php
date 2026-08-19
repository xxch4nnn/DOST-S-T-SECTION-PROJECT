<?php

namespace App\Livewire\Scholars;

use App\Models\ClearanceStatus;
use App\Models\Course;
use App\Models\Scholar;
use App\Models\School;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';

    public array $expandedYears = [];

    public array $selectedYears = [];

    public function mount(): void
    {
        $firstYear = Scholar::query()->distinct()->pluck('year_of_award')->sortDesc()->first();
        if ($firstYear) {
            $this->expandedYears[] = (string) $firstYear;
        }
    }

    public function toggleYear(string $year): void
    {
        if (in_array($year, $this->expandedYears, true)) {
            $this->expandedYears = array_values(array_diff($this->expandedYears, [$year]));
        } else {
            $this->expandedYears[] = $year;
        }
    }

    public function toggleSelectYear(string $year): void
    {
        if (in_array($year, $this->selectedYears, true)) {
            $this->selectedYears = array_values(array_diff($this->selectedYears, [$year]));
        } else {
            $this->selectedYears[] = $year;
        }
    }

    public function selectAllYears(array $years): void
    {
        $this->selectedYears = array_map('strval', $years);
    }

    public function deselectAllYears(): void
    {
        $this->selectedYears = [];
    }

    public function openScholar($scholarId): void
    {
        $scholarId = (int) $scholarId;
        $scholar = Scholar::with(['scholarshipType', 'school', 'course', 'region', 'clearanceStatus'])->find($scholarId);

        $scholarData = null;
        if ($scholar) {
            $scholarData = [
                'id' => $scholar->id,
                'name' => "{$scholar->last_name}, {$scholar->first_name} {$scholar->middle_name}",
                'spas_id' => $scholar->spas_no,
                'program' => $scholar->scholarshipType->code ?? ($scholar->scholarshipType->name ?? '—'),
                'program_type' => $scholar->scholarshipType->name ?? '—',
                'year_of_award' => $scholar->year_of_award ?? '—',
                'clearance_date' => $scholar->clearance_date ? $scholar->clearance_date->format('d / m / Y') : '—',
                'course' => $scholar->course->name ?? '—',
                'university' => $scholar->school->name ?? '—',
                'address' => $scholar->barangay ? trim("{$scholar->barangay}, {$scholar->district}", ' ,') : '—',
                'municipality' => $scholar->municipality ?? '—',
                'province' => $scholar->province ?? '—',
                'region' => $scholar->region->name ?? '—',
                'email' => $scholar->email_address ?? '—',
                'contact' => $scholar->contact_number ?? '—',
                'birthdate' => $scholar->birthdate ? $scholar->birthdate->format('m / d / Y') : '—',
                'sex' => $scholar->sex ?? '—',
                'status' => $scholar->clearanceStatus->name ?? '—',
            ];
        }

        $this->dispatch('open-scholar-drawer', scholarId: $scholarId, scholarData: $scholarData);
    }

    public function render()
    {
        $searchTerm = '%'.$this->search.'%';

        // 2. Create the array of 11 bindings for the WHERE clause
        $bindings = array_fill(0, 11, $searchTerm);

        // 3. Execute and hydrate the Scholar models
        $scholars = Scholar::fromQuery('
            SELECT 
                s.*,
                clearance_statuses.name as clearance_status,
                schools.name as school,
                courses.name as course,
                scholarships.name as scholarship,
                scholarship_types.name as scholarship_type,
                regions.name as region
            FROM scholars as s
            INNER JOIN schools ON s.school_id = schools.id
            INNER JOIN scholarship_types ON s.scholarship_type_id = scholarship_types.id
            INNER JOIN scholarships ON s.scholarship_id = scholarships.id
            INNER JOIN courses ON s.course_id = courses.id
            INNER JOIN regions ON s.region_id = regions.id
            INNER JOIN clearance_statuses ON s.clearance_status_id = clearance_statuses.id
            WHERE (
                s.last_name LIKE ? 
                OR s.first_name LIKE ? 
                OR s.middle_name LIKE ? 
                OR s.generational_suffix LIKE ? 
                OR s.spas_no LIKE ? 
                OR scholarship_types.name LIKE ? 
                OR scholarships.name LIKE ? 
                OR s.contact_number LIKE ? 
                OR s.email_address LIKE ? 
                OR schools.name LIKE ? 
                OR courses.name LIKE ?
            )
            ORDER BY year_of_award DESC, last_name ASC
            LIMIT 10
        ', $bindings);

        // Group scholars by year_of_award for the new Folder UI
        $groupedScholars = $scholars->groupBy(function ($scholar) {
            return (string) ($scholar->year_of_award ?? 'Unknown');
        });

        if ($groupedScholars->isEmpty()) {
            $groupedScholars = collect(['2023' => collect([])]);
        }

        return view('livewire.scholars.index', [
            'groupedScholars' => $groupedScholars,
            'allYears' => $groupedScholars->keys()->toArray(),
            'schools' => School::orderBy('name', 'asc')->get(),
            'courses' => Course::orderBy('name', 'asc')->get(),
        ])->layout('layouts.app');
    }

    private function getMockScholars()
    {
        $mockNames = [
            ['first' => 'Wakin Cean', 'last' => 'Maclang', 'mid' => 'C'],
            ['first' => 'John Doe', 'last' => 'Smith', 'mid' => 'A'],
            ['first' => 'Jane', 'last' => 'Doe', 'mid' => 'B'],
            ['first' => 'Alice', 'last' => 'Johnson', 'mid' => 'D'],
            ['first' => 'Bob', 'last' => 'Williams', 'mid' => 'E'],
            ['first' => 'Charlie', 'last' => 'Brown', 'mid' => 'F'],
            ['first' => 'David', 'last' => 'Davis', 'mid' => 'G'],
            ['first' => 'Eve', 'last' => 'Miller', 'mid' => 'H'],
            ['first' => 'Frank', 'last' => 'Wilson', 'mid' => 'I'],
        ];

        $mockScholars = collect();
        foreach ($mockNames as $index => $name) {
            $scholar = new Scholar([
                'first_name' => $name['first'],
                'last_name' => $name['last'],
                'middle_name' => $name['mid'],
                'spas_no' => '2023-00855-'.(2235 + $index),
                'year_of_award' => '2023',
            ]);
            $scholar->id = $index + 1;

            $programName = $index % 2 === 0 ? 'RA 10612' : 'RA 7687';
            $statusName = $index % 3 === 0 ? 'Cleared' : 'Not Cleared';

            $scholarship = new Scholarship(['name' => $programName]);
            $scholarshipType = new ScholarshipType(['name' => 'DOST - SEI Undergraduate Scholarship']);
            $clearanceStatus = new ClearanceStatus(['name' => $statusName]);

            $scholar->setRelation('scholarship', $scholarship);
            $scholar->setRelation('scholarshipType', $scholarshipType);
            $scholar->setRelation('clearanceStatus', $clearanceStatus);

            $mockScholars->push($scholar);
        }

        return $mockScholars;
    }
}
