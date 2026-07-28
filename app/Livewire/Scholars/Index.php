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

    public function openScholar(int $scholarId): void
    {
        $this->dispatch('open-scholar-drawer', scholarId: $scholarId);
    }

    public function render()
    {
        $query = Scholar::query()
            ->with(['scholarshipProgram', 'scholarshipProgramType', 'scholarshipType', 'school', 'course', 'clearanceStatus'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('first_name', 'like', '%'.$this->search.'%')
                        ->orWhere('last_name', 'like', '%'.$this->search.'%')
                        ->orWhere('middle_name', 'like', '%'.$this->search.'%')
                        ->orWhere('spas_no', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->spas_no, function ($query) {
                $query->where('spas_no', 'like', '%'.$this->spas_no.'%');
            })
            ->when($this->school_id, function ($query) {
                $query->where('school_id', $this->school_id);
            })
            ->when($this->course_id, function ($query) {
                $query->where('course_id', $this->course_id);
            })
            ->orderBy('last_name')
            ->paginate(15);

        return view('livewire.scholars.index', [
            'groupedScholars' => $groupedScholars,
            'allYears' => $groupedScholars->keys()->toArray(),
        ])->layout('layouts.app');
    }
}
