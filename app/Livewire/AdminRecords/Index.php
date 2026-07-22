<?php

namespace App\Livewire\AdminRecords;

use App\Models\AdministrativeRecord;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public $search = '';
    public $record_type = '';
    public $year = '';

    public function updating($field)
    {
        if (in_array($field, ['search', 'record_type', 'year'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $records = AdministrativeRecord::query()
            ->with(['creator'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('series_number', 'like', '%' . $this->search . '%')
                      ->orWhere('recipient', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->record_type, function ($query) {
                $query->where('record_type', $this->record_type);
            })
            ->when($this->year, function ($query) {
                $query->where('year', $this->year);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.admin-records.index', [
            'records' => $records,
            // Predefined categories from client proposal
            'recordTypes' => ['Memorandum', 'Special Order', 'Financial Report', 'Payroll', 'Endorsement', 'Communications'],
        ])->layout('layouts.app');
    }
}
