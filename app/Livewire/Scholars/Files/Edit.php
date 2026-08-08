<?php

namespace App\Livewire\Scholars\Files;

use Livewire\Component;

/**
 * Stub reserved for PR-E canvas/file editor. Not routed; mount always 501.
 */
class Edit extends Component
{
    public function mount($scholar = null, $file = null): void
    {
        abort(501, 'Document file editor is under construction.');
    }

    public function render()
    {
        return view('livewire.scholars.files.edit')->layout('layouts.app');
    }
}
