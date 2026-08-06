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

    public function mount($scholar, $file): void
    {
        abort(501, 'Document file editor is under construction.');
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
