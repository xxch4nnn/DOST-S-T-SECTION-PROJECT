<?php

namespace App\Livewire\Scholars\Files;

use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public $scholar_id;

    public $file_id;

    #[Url]
    public $return_url = '/scholars';

    // Mock Data models
    public $fileTypes = [];

    public $file;

    // Form fields
    public $file_type_id = '';

    public $file_name = '';

    public $mime_type = '';

    public $file_size = '';

    public $compiledFile;

    public function mount($scholar, $file)
    {
        $this->scholar_id = $scholar;
        $this->file_id = $file;

        // Mock data
        $this->fileTypes = collect([
            (object) ['id' => 1, 'name' => 'Grades', 'file_group_id' => 1],
            (object) ['id' => 2, 'name' => 'Registration Form', 'file_group_id' => 1],
            (object) ['id' => 3, 'name' => 'Clearance', 'file_group_id' => 2],
        ]);

        $this->file = (object) [
            'id' => $file,
            'file_name' => '1st_sem_grades.pdf',
            'file_type_id' => 1,
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'fileType' => (object) ['file_group_id' => 1],
        ];

        $this->file_type_id = $this->file->file_type_id;
        $this->file_name = $this->file->file_name;
        $this->mime_type = $this->file->mime_type;
        $this->file_size = $this->file->file_size;
    }

    public function save()
    {
        $this->validate([
            'file_type_id' => 'required',
            'file_name' => 'required|string',
            'compiledFile' => 'required|file',
        ]);

        // Mock Save Logic
        // In reality, this is where we'd move the file from Livewire's temporary directory
        // $this->compiledFile->storeAs('scholars/files', $this->file_name);

        session()->flash('success', 'File compiled and uploaded successfully via Livewire!');

        // Return or redirect
        return redirect()->route('scholars.files.edit', [
            'scholar' => $this->scholar_id,
            'file' => $this->file_id,
        ]);
    }

    public function render()
    {
        return view('livewire.scholars.files.edit')->layout('layouts.app');
    }
}
