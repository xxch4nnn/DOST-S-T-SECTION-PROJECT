<?php

namespace App\Livewire\AdminRecords;

use App\Models\AdministrativeRecord;
use Livewire\Component;

class Edit extends Component
{
    public AdministrativeRecord $record;

    public $record_type = '';

    public $series_number = '';

    public $title = '';

    public $recipient = '';

    public $year = '';

    public $quarter = '';

    public $for_disposal = false;

    public function mount(AdministrativeRecord $record)
    {
        $this->record = $record;
        $this->fill($record->toArray());
    }

    public function save()
    {
        $validated = $this->validate([
            'record_type' => 'required|string|max:100',
            'series_number' => 'nullable|string|max:100',
            'title' => 'required|string|max:255',
            'recipient' => 'nullable|string|max:150',
            'year' => 'nullable|integer',
            'quarter' => 'nullable|string|max:10',
            'for_disposal' => 'boolean',
        ]);

        // Clean up empty optional fields
        foreach ($validated as $key => $value) {
            if ($value === '') {
                $validated[$key] = null;
            }
        }

        $this->record->update($validated);

        return redirect()->route('admin-records.show', $this->record->id);
    }

    public function render()
    {
        return view('livewire.admin-records.edit', [
            'recordTypes' => ['Memorandum', 'Special Order', 'Financial Report', 'Payroll', 'Endorsement', 'Communications'],
        ])->layout('layouts.app');
    }
}
