<?php

use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component
{
    public bool $isOpen = false;
    public string $title = 'Amendatory Agreement';
    public string $scholarName = '';
    public string $imageUrl = '';
    public int $zoom = 100;
    public int $currentPage = 1;
    public int $totalPages = 10;

    #[On('open-document-viewer')]
    public function openViewer(string $title, string $imageUrl = '', string $scholarName = ''): void
    {
        $this->title = $title;
        $this->imageUrl = $imageUrl;
        $this->scholarName = $scholarName;
        $this->isOpen = true;
        $this->zoom = 100;
        $this->currentPage = 1;
    }

    #[On('close-document-viewer')]
    public function closeViewer(): void
    {
        $this->isOpen = false;
    }

    public function setPage(int $page): void
    {
        if ($page >= 1 && $page <= $this->totalPages) {
            $this->currentPage = $page;
        }
    }

    public function zoomIn(): void
    {
        if ($this->zoom < 200) {
            $this->zoom += 25;
        }
    }

    public function zoomOut(): void
    {
        if ($this->zoom > 50) {
            $this->zoom -= 25;
        }
    }

    public function prevPage(): void
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function nextPage(): void
    {
        if ($this->currentPage < $this->totalPages) {
            $this->currentPage++;
        }
    }
}; ?>

<div>
    @if($isOpen)
        {{-- Overlay Backdrop (z-index 1045: behind Scholar Drawer) --}}
        <div wire:click="closeViewer" class="doc-viewer-backdrop"></div>

        {{-- Top Left Close Button --}}
        <button wire:click="closeViewer" type="button" class="btn btn-link text-white p-2 border-0 shadow-none doc-viewer-close-btn" title="Close Viewer">
            <i class="ph ph-x fs-3"></i>
        </button>

        {{-- Far Left Page Thumbnails Sidebar --}}
        <div class="doc-viewer-sidebar">
            <div wire:click="setPage(1)" class="doc-viewer-thumb {{ $currentPage === 1 ? 'doc-viewer-thumb--active' : '' }}" role="button">
                <div class="thumb-mini-paper">
                    <div class="thumb-mini-logo"></div>
                    <div class="thumb-mini-line"></div>
                    <div class="thumb-mini-line"></div>
                </div>
                <span class="thumb-num">1</span>
            </div>

            <div wire:click="setPage(2)" class="doc-viewer-thumb {{ $currentPage === 2 ? 'doc-viewer-thumb--active' : '' }}" role="button">
                <div class="thumb-mini-paper">
                    <div class="thumb-mini-logo"></div>
                    <div class="thumb-mini-line"></div>
                    <div class="thumb-mini-line"></div>
                </div>
                <span class="thumb-num">2</span>
            </div>

            <div wire:click="setPage(3)" class="doc-viewer-thumb {{ $currentPage === 3 ? 'doc-viewer-thumb--active' : '' }}" role="button">
                <div class="thumb-mini-paper">
                    <div class="thumb-mini-logo"></div>
                    <div class="thumb-mini-line"></div>
                    <div class="thumb-mini-line"></div>
                </div>
                <span class="thumb-num">3</span>
            </div>
        </div>

        {{-- Top Control Toolbar --}}
        <div class="doc-viewer-toolbar">
            <span class="small fw-semibold text-secondary me-2">Page {{ $currentPage }} / {{ $totalPages }}</span>
            <span class="text-muted opacity-25 me-1">|</span>

            <div class="doc-viewer-toolbar__group me-1">
                <button wire:click="zoomOut" type="button" class="btn btn-link text-dark p-0 border-0 shadow-none me-1" title="Zoom Out">
                    <i class="ph ph-minus fs-6"></i>
                </button>
                <span class="small fw-semibold me-1">{{ $zoom }}%</span>
                <button wire:click="zoomIn" type="button" class="btn btn-link text-dark p-0 border-0 shadow-none" title="Zoom In">
                    <i class="ph ph-plus fs-6"></i>
                </button>
            </div>

            <div class="doc-viewer-toolbar__divider me-1"></div>

            <button type="button" class="btn btn-link text-dark p-1 border-0 shadow-none" title="Print Document">
                <i class="ph ph-printer fs-5"></i>
            </button>
            <button type="button" class="btn btn-link text-dark p-1 border-0 shadow-none" title="Download Document">
                <i class="ph ph-download-simple fs-5"></i>
            </button>
            <button type="button" class="btn btn-link text-dark p-1 border-0 shadow-none" title="Rotate Document">
                <i class="ph ph-arrow-clockwise fs-5"></i>
            </button>
            <button type="button" class="btn btn-link text-danger p-1 border-0 shadow-none" title="Delete Document">
                <i class="ph ph-trash fs-5"></i>
            </button>

            <div class="doc-viewer-toolbar__divider mx-1"></div>

            <button wire:click="prevPage" type="button" class="btn btn-link text-dark p-1 border-0 shadow-none {{ $currentPage <= 1 ? 'disabled opacity-25' : '' }}" title="Previous Page">
                <i class="ph ph-caret-left fs-5"></i>
            </button>
            <button wire:click="nextPage" type="button" class="btn btn-link text-dark p-1 border-0 shadow-none {{ $currentPage >= $totalPages ? 'disabled opacity-25' : '' }}" title="Next Page">
                <i class="ph ph-caret-right fs-5"></i>
            </button>
        </div>

        {{-- Document Display Paper Canvas --}}
        <div class="doc-viewer-canvas" style="transform: scale({{ $zoom / 100 }});">
            <div class="doc-viewer-paper shadow-lg d-flex flex-column align-items-center justify-content-center text-center">
                @if($imageUrl && $imageUrl !== 'https://images.unsplash.com/photo-1568667256549-094345857637?q=80&w=1000&auto=format&fit=crop')
                    <img src="{{ $imageUrl }}" alt="{{ $title }}" class="img-fluid rounded shadow-sm" style="max-height: 100%;">
                @else
                    {{-- Blank Slate Container Placeholder for Document Files --}}
                    <div class="my-auto py-5 text-secondary">
                        <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 4.5rem; height: 4.5rem;">
                            <i class="ph ph-file-pdf display-4 text-muted"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">{{ $title ?: 'Document Container' }}</h5>
                        <p class="text-muted small mb-3">Blank slate container for document files</p>
                        <span class="badge bg-light text-secondary border px-3 py-2 fw-medium" style="font-size: 0.75rem;">
                            <i class="ph ph-files me-1"></i> Ready for document upload / preview
                        </span>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>


