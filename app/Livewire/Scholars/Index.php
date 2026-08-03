<?php

namespace App\Livewire\Scholars;

use App\Models\ClearanceStatus;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\ScholarshipType;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';

    public array $expandedYears = [];

    public array $selectedYears = [];

    public function mount(): void
    {
        // Default expand the first year group if available
        $firstYear = Scholar::query()->distinct()->pluck('year_of_award')->sortDesc()->first();
        if ($firstYear) {
            $this->expandedYears[] = (string) $firstYear;
        } else {
            $this->expandedYears[] = '2023';
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
        // Find the scholar in DB, or fallback to mock data
        $scholar = Scholar::with(['scholarshipType', 'school', 'course', 'region', 'clearanceStatus'])->find($scholarId);

        $scholarData = null;
        if ($scholar) {
            $scholarData = [
                'id' => $scholar->id,
                'name' => "{$scholar->last_name}, {$scholar->first_name} {$scholar->middle_name}",
                'spas_id' => $scholar->spas_no,
                'program' => $scholar->scholarshipType->code ?? 'RA 10612',
                'program_type' => $scholar->scholarshipType->name ?? 'DOST - SEI Undergraduate Scholarship',
                'year_of_award' => $scholar->year_of_award ?? '2023',
                'clearance_date' => $scholar->clearance_date ? $scholar->clearance_date->format('d / m / Y') : '23 / 06 / 2027',
                'course' => $scholar->course->name ?? 'BS in Computer Science Major in Data Science',
                'university' => $scholar->school->name ?? 'University of Southeastern Philippines',
                'address' => $scholar->barangay ? "{$scholar->barangay}, {$scholar->district}" : 'Brgy. 34 - D, C.M. Recto St. Poblacion District',
                'municipality' => $scholar->municipality ?? 'Davao City',
                'province' => $scholar->province ?? 'Davao del Sur',
                'region' => $scholar->region->name ?? 'Region XI - Davao Region',
                'email' => $scholar->email_address ?? 'example@gmail.com',
                'contact' => $scholar->contact_number ?? '09000000000',
                'birthdate' => $scholar->birthdate ? $scholar->birthdate->format('m / d / Y') : '01 / 01 / 2000',
                'sex' => $scholar->sex ?? 'Male',
                'status' => $scholar->clearanceStatus->name ?? 'Not Cleared',
            ];
        } else {
            // Find in mock data
            $mockScholars = $this->getMockScholars();
            $mockScholar = $mockScholars->firstWhere('id', $scholarId);
            if ($mockScholar) {
                $scholarData = [
                    'id' => $mockScholar->id,
                    'name' => "{$mockScholar->last_name}, {$mockScholar->first_name} {$mockScholar->middle_name}",
                    'spas_id' => $mockScholar->spas_no,
                    'program' => $mockScholar->scholarship->name,
                    'program_type' => $mockScholar->scholarshipType->name,
                    'year_of_award' => $mockScholar->year_of_award,
                    'clearance_date' => '23 / 06 / 2027',
                    'course' => 'BS in Computer Science Major in Data Science',
                    'university' => 'University of Southeastern Philippines',
                    'address' => 'Brgy. 34 - D, C.M. Recto St. Poblacion District',
                    'municipality' => 'Davao City',
                    'province' => 'Davao del Sur',
                    'region' => 'Region XI - Davao Region',
                    'email' => strtolower($mockScholar->first_name).'@gmail.com',
                    'contact' => '09762941445',
                    'birthdate' => '08 / 23 / 2005',
                    'sex' => 'Male',
                    'status' => $mockScholar->clearanceStatus->name,
                ];
            }
        }

        $this->dispatch('open-scholar-drawer', scholarId: $scholarId, scholarData: $scholarData);
    }

    public function render()
    {
        $query = Scholar::query()
            ->with(['scholarship', 'scholarshipType', 'school', 'course', 'clearanceStatus'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('first_name', 'like', '%'.$this->search.'%')
                        ->orWhere('last_name', 'like', '%'.$this->search.'%')
                        ->orWhere('middle_name', 'like', '%'.$this->search.'%')
                        ->orWhere('spas_no', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('year_of_award', 'desc')
            ->orderBy('last_name', 'asc');

        $scholars = $query->get();

        // Group scholars by year_of_award
        $groupedScholars = $scholars->groupBy(function ($scholar) {
            return (string) ($scholar->year_of_award ?? '2023');
        });

        // Ensure default year 2023 exists if DB is empty
        if ($groupedScholars->isEmpty()) {
            $mockScholars = $this->getMockScholars();
            $groupedScholars = collect(['2023' => $mockScholars]);
        }

        return view('livewire.scholars.index', [
            'groupedScholars' => $groupedScholars,
            'allYears' => $groupedScholars->keys()->toArray(),
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
