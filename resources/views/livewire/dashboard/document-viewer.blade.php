<?php

use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component
{
    public bool $isOpen = false;
    public ?string $fileType = 'pdf'; // 'pdf', 'image', or null
    public string $title = 'Amendatory Agreement';
    public int $documentIndex = 1;
    public string $scholarName = '';
    public string $fileUrl = '';
    public int $zoom = 100;
    public int $rotation = 0;
    public int $currentPage = 1;
    public int $totalPages = 10;
    public array $thumbnails = [];
    public array $images = [];
    public int $currentImageIndex = 0;

    public int $scholarId = 1;
    public int $fileId = 4;

    #[On('open-document-viewer')]
    public function openViewer(
        string $title = 'Amendatory Agreement',
        string $fileUrl = '',
        string $scholarName = '',
        ?string $fileType = null,
        int $documentIndex = 1,
        int $scholarId = 1,
        int $fileId = 4,
        array $extraData = []
    ): void {
        $this->title = $title;
        $this->fileUrl = $fileUrl;
        $this->scholarName = $scholarName;
        $this->documentIndex = $documentIndex;
        $this->scholarId = $scholarId;
        $this->fileId = $fileId;
        
        // Auto-detect fileType if not explicitly passed or if fileUrl is an image format
        if (!$fileType) {
            $extension = strtolower(pathinfo(parse_url($fileUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'], true)) {
                $fileType = 'image';
            } else {
                $fileType = 'pdf';
            }
        }
        
        $this->fileType = strtolower($fileType);
        $this->isOpen = true;
        $this->zoom = 100;
        $this->rotation = 0;
        $this->currentPage = $extraData['currentPage'] ?? 1;
        $this->totalPages = $extraData['totalPages'] ?? 10;
        $this->thumbnails = $extraData['thumbnails'] ?? [];
        $this->images = $extraData['images'] ?? ($fileUrl ? [$fileUrl] : []);
        $this->currentImageIndex = $extraData['currentImageIndex'] ?? 0;
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

    public function prevImage(): void
    {
        if (count($this->images) > 0) {
            $this->currentImageIndex = ($this->currentImageIndex - 1 + count($this->images)) % count($this->images);
            $this->fileUrl = $this->images[$this->currentImageIndex] ?? $this->fileUrl;
        }
    }

    public function nextImage(): void
    {
        if (count($this->images) > 0) {
            $this->currentImageIndex = ($this->currentImageIndex + 1) % count($this->images);
            $this->fileUrl = $this->images[$this->currentImageIndex] ?? $this->fileUrl;
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

    public function rotate(): void
    {
        $this->rotation = ($this->rotation + 90) % 360;
    }

    public function printDocument(): void
    {
        $this->dispatch('print-requested', url: $this->fileUrl);
    }

    public function downloadDocument(): void
    {
        $this->dispatch('download-requested', url: $this->fileUrl);
    }

    public function editFile(): void
    {
        $currentUrl = request()->header('referer') ?? '/scholars';
        $returnUrl = parse_url($currentUrl, PHP_URL_PATH) ?? '/scholars';
        $this->redirect("/scholars/{$this->scholarId}/files/{$this->fileId}/edit?return_url=" . urlencode($returnUrl));
    }

    public function deleteDocument(): void
    {
        $this->dispatch('delete-requested', fileUrl: $this->fileUrl);
    }
}; ?>

<div>
    @if($isOpen)
        {{-- Overlay Backdrop --}}
        <div wire:click="closeViewer" class="doc-viewer-backdrop"></div>

        {{-- Top Left Close Button (Always visible at top-left) --}}
        <button wire:click="closeViewer" type="button" class="doc-viewer-close-btn" title="Close Viewer">
            <i class="ph ph-x fs-2"></i>
        </button>

        {{-- ========================================================= --}}
        {{-- LEFT SIDEBAR: PAGE THUMBNAILS CONTAINER                   --}}
        {{-- Rendered ONLY for Multi-Page PDFs (totalPages > 1)        --}}
        {{-- Hidden for Single-Page PDFs & Images                      --}}
        {{-- ========================================================= --}}
        @if($fileType === 'pdf' && $totalPages > 1)
            <div class="doc-viewer-sidebar">
                @if(count($thumbnails) > 0)
                    @foreach($thumbnails as $index => $thumb)
                        @php $pageNum = $thumb['page'] ?? ($index + 1); @endphp
                        <div wire:click="setPage({{ $pageNum }})" class="doc-viewer-thumb {{ $currentPage === $pageNum ? 'doc-viewer-thumb--active' : '' }}" role="button">
                            @if(isset($thumb['url']) && $thumb['url'])
                                <img src="{{ $thumb['url'] }}" alt="Page {{ $pageNum }}" class="img-fluid rounded">
                            @else
                                <div class="thumb-mini-paper">
                                    <div class="thumb-mini-logo"></div>
                                    <div class="thumb-mini-line"></div>
                                    <div class="thumb-mini-line short"></div>
                                </div>
                            @endif
                            <span class="thumb-num">{{ $pageNum }}</span>
                        </div>
                    @endforeach
                @else
                    {{-- Default dynamic thumbnail list (larger size matching reference) --}}
                    @for($i = 1; $i <= min(3, max(1, $totalPages)); $i++)
                        <div wire:click="setPage({{ $i }})" class="doc-viewer-thumb {{ $currentPage === $i ? 'doc-viewer-thumb--active' : '' }}" role="button">
                            <div class="thumb-mini-paper">
                                <div class="thumb-mini-logo"></div>
                                <div class="thumb-mini-line"></div>
                                <div class="thumb-mini-line short"></div>
                            </div>
                            <span class="thumb-num">{{ $i }}</span>
                        </div>
                    @endfor
                @endif
            </div>
        @endif

        {{-- ========================================================= --}}
        {{-- TOP CONTROL TOOLBAR                                       --}}
        {{-- ========================================================= --}}
        <div class="doc-viewer-toolbar {{ ($fileType === 'image' || $totalPages <= 1) ? 'doc-viewer-toolbar--collapsed-sidebar' : '' }}">
            
            {{-- MULTI-PAGE PDF ONLY: Page Counter ("Page X / Y") --}}
            @if($fileType === 'pdf' && $totalPages > 1)
                <span class="small fw-semibold text-secondary me-2">Page {{ $currentPage }} / {{ $totalPages }}</span>
                <div class="doc-viewer-toolbar__divider me-1"></div>
            @endif

            {{-- Zoom Controls --}}
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

            {{-- Action Tools: Print, Download, Upload, Delete --}}
            <button wire:click="printDocument" type="button" class="btn btn-link text-dark p-1 border-0 shadow-none" title="Print">
                <i class="ph ph-printer fs-5"></i>
            </button>
            <button wire:click="downloadDocument" type="button" class="btn btn-link text-dark p-1 border-0 shadow-none" title="Download">
                <i class="ph ph-download-simple fs-5"></i>
            </button>
            <button wire:click="editFile" type="button" class="btn btn-link text-dark p-1 border-0 shadow-none" title="Edit File">
                <i class="ph ph-pencil-simple fs-5"></i>
            </button>
            <button wire:click="deleteDocument" type="button" class="btn btn-link text-danger p-1 border-0 shadow-none" title="Delete">
                <i class="ph ph-trash fs-5"></i>
            </button>

            {{-- MULTI-PAGE PDF ONLY: Page Stepping Arrows (< >) --}}
            @if($fileType === 'pdf' && $totalPages > 1)
                <div class="doc-viewer-toolbar__divider mx-1"></div>
                <button wire:click="prevPage" type="button" class="btn btn-link text-dark p-1 border-0 shadow-none {{ $currentPage <= 1 ? 'disabled opacity-25' : '' }}" title="Previous Page">
                    <i class="ph ph-caret-left fs-5"></i>
                </button>
                <button wire:click="nextPage" type="button" class="btn btn-link text-dark p-1 border-0 shadow-none {{ $currentPage >= $totalPages ? 'disabled opacity-25' : '' }}" title="Next Page">
                    <i class="ph ph-caret-right fs-5"></i>
                </button>
            @endif
        </div>

        {{-- FLOATING OVERLAY CHEVRONS (< >) ON CANVAS FLANKS --}}
        <button wire:click="{{ $fileType === 'image' ? 'prevImage' : 'prevPage' }}" type="button" class="doc-viewer-overlay-arrow doc-viewer-overlay-arrow--left" title="Previous">
            <i class="ph ph-caret-left fs-2 text-white"></i>
        </button>
        <button wire:click="{{ $fileType === 'image' ? 'nextImage' : 'nextPage' }}" type="button" class="doc-viewer-overlay-arrow doc-viewer-overlay-arrow--right" title="Next">
            <i class="ph ph-caret-right fs-2 text-white"></i>
        </button>

        {{-- ========================================================= --}}
        {{-- MAIN CANVAS DISPLAY AREA                                  --}}
        {{-- ========================================================= --}}
        <div class="doc-viewer-canvas-wrapper {{ ($fileType === 'image' || $totalPages <= 1) ? 'doc-viewer-canvas-wrapper--full' : '' }}">
            
            {{-- OPTION 1: IMAGE FILE MODE --}}
            @if($fileType === 'image')
                <div class="doc-viewer-image-container">
                    <div class="doc-viewer-image-paper shadow-lg" style="transform: scale({{ $zoom / 100 }}) rotate({{ $rotation }}deg); transition: transform 0.2s ease;">
                        @if($fileUrl)
                            <img src="{{ $fileUrl }}" alt="{{ $title }}" class="img-fluid">
                        @else
                            <div class="my-auto py-5 text-secondary text-center">
                                <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 4.5rem; height: 4.5rem;">
                                    <i class="ph ph-image-square display-4 text-muted"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">{{ $title ?: 'Scanned Image' }} - Image {{ ($currentImageIndex ?: 0) + 1 }}</h5>
                                <p class="text-muted small mb-3">Blank slate container for image files</p>
                                <span class="badge bg-light text-secondary border px-3 py-2 fw-medium" style="font-size: 0.75rem;">
                                    <i class="ph ph-image me-1"></i> Ready for image upload / preview
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

            {{-- OPTION 2: PDF MODE WITH PREVIEW --}}
            @elseif($fileType === 'pdf' && $fileUrl)
                <div class="doc-viewer-canvas" style="transform: scale({{ $zoom / 100 }}); transform-origin: top left;">
                    <div class="doc-viewer-paper shadow-lg text-center">
                        <img src="{{ $fileUrl }}" alt="{{ $title }}" class="img-fluid rounded shadow-sm" style="max-height: 100%;">
                    </div>
                </div>

            {{-- OPTION 3: PDF MODE BLANK SLATE FALLBACK --}}
            @else
                <div class="doc-viewer-canvas" style="transform: scale({{ $zoom / 100 }}); transform-origin: top left;">
                    <div class="doc-viewer-paper shadow-lg text-center">
                        <div class="my-auto py-5 text-secondary">
                            <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 4.5rem; height: 4.5rem;">
                                <i class="ph ph-file-pdf display-4 text-muted"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">{{ $title ?: 'Amendatory Agreement' }} - Document {{ $documentIndex ?: 1 }}</h5>
                            <p class="text-muted small mb-3">Blank slate container for document files</p>
                            <span class="badge bg-light text-secondary border px-3 py-2 fw-medium" style="font-size: 0.75rem;">
                                <i class="ph ph-files me-1"></i> Ready for document upload / preview
                            </span>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    @endif
</div>
