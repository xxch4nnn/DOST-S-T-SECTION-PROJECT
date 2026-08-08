<?php

namespace App\Livewire\Scholars;

use App\Models\Scholar;
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

        $groupedScholars = $scholars->groupBy(function ($scholar) {
            return (string) ($scholar->year_of_award ?? 'Unknown');
        });

        return view('livewire.scholars.index', [
            'groupedScholars' => $groupedScholars,
            'allYears' => $groupedScholars->keys()->toArray(),
        ])->layout('layouts.app');
    }
}
