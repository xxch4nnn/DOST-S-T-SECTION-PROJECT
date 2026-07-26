<?php

namespace App\Livewire\AdminRecords;

use App\Models\AdministrativeRecord;
use Livewire\Component;

class Index extends Component
{
    public ?string $selectedCategory = null;

    public string $search = '';

    public array $expandedYears = [];

    public array $selectedYears = [];

    public array $selectedCards = [];

    public array $categories = [
        'Annual Financial Reports',
        'Memorandum',
        'Quarterly Financial Reports',
        'Payrolls',
        'Endorsements',
        'Communications',
    ];

    public function mount(): void
    {
        $this->expandedYears = ['2023'];
    }

    public function selectCategory(?string $category): void
    {
        $this->selectedCategory = $category;
        $this->search = '';
        $this->selectedYears = [];
        $this->selectedCards = [];
    }

    public function clearCategory(): void
    {
        $this->selectedCategory = null;
        $this->selectedYears = [];
        $this->selectedCards = [];
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

    public function toggleSelectCard(int $cardId): void
    {
        if (in_array($cardId, $this->selectedCards, true)) {
            $this->selectedCards = array_values(array_diff($this->selectedCards, [$cardId]));
        } else {
            $this->selectedCards[] = $cardId;
        }
    }

    public function selectAllYears(array $years): void
    {
        $this->selectedYears = array_map('strval', $years);
    }

    public function deselectAll(): void
    {
        $this->selectedYears = [];
        $this->selectedCards = [];
    }

    public function openRecord(int $recordId): void
    {
        $this->dispatch('open-admin-file-drawer', recordId: $recordId);
    }

    public function render()
    {
        $query = AdministrativeRecord::query()
            ->when($this->selectedCategory, function ($q) {
                $q->where('record_type', 'like', '%'.$this->selectedCategory.'%');
            })
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('series_number', 'like', '%'.$this->search.'%')
                        ->orWhere('recipient', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('year', 'desc')
            ->orderBy('created_at', 'desc');

        $records = $query->get();

        // Group records by year
        $groupedRecords = $records->groupBy(function ($record) {
            return (string) ($record->year ?? '2023');
        });

        if ($groupedRecords->isEmpty()) {
            $groupedRecords = collect(['2023' => collect([])]);
        }

        return view('livewire.admin-records.index', [
            'groupedRecords' => $groupedRecords,
            'allYears' => $groupedRecords->keys()->toArray(),
        ])->layout('layouts.app');
    }
}
