<?php

namespace App\Livewire\AdminRecords;

use App\Models\AdministrativeRecord;
use Livewire\Component;

class Create extends Component
{
    public $record_type = '';

    public $series_number = '';

    public $title = '';

    public $recipient = '';

    public $year = '';

    public $quarter = '';

    public $for_disposal = false;

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

        $validated['created_by'] = auth()->id();

        // Clean up empty optional fields
        foreach ($validated as $key => $value) {
            if ($value === '') {
                $validated[$key] = null;
            }
        }

        $record = AdministrativeRecord::create($validated);

        return redirect()->route('admin-records.show', $record->id);
    }

    public function render()
    {
        return view('livewire.admin-records.create', [
            'recordTypes' => ['Memorandum', 'Special Order', 'Financial Report', 'Payroll', 'Endorsement', 'Communications'],
        ])->layout('layouts.app');
    }
}
