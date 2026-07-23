<?php

namespace App\Livewire\Scholars;

use App\Models\Course;
use App\Models\Scholar;
use App\Models\School;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public $search = '';

    public $spas_no = '';

    public $school_id = '';

    public $course_id = '';

    public function updating($field)
    {
        if (in_array($field, ['search', 'spas_no', 'school_id', 'course_id'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $scholars = Scholar::query()
            ->with(['scholarship', 'school', 'course', 'clearanceStatus'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%'.$this->search.'%')
                        ->orWhere('last_name', 'like', '%'.$this->search.'%')
                        ->orWhere('middle_name', 'like', '%'.$this->search.'%');
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
            'scholars' => $scholars,
            'schools' => School::orderBy('name')->get(),
            'courses' => Course::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
