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
        $this->currentPage = 1;
        $this->totalPages = $this->calculateTotalPages();
        $this->thumbnails = [];
        $this->images = $fileUrl ? [$fileUrl] : [];
        $this->currentImageIndex = 0;
    }

    protected function calculateTotalPages(): int
    {
        if ($this->fileType === 'image') {
            return 1;
        }

        $fileModel = \App\Models\File::find($this->fileId);
        if ($fileModel && \Illuminate\Support\Facades\Storage::disk('local')->exists($fileModel->file_path)) {
            $path = \Illuminate\Support\Facades\Storage::disk('local')->path($fileModel->file_path);
            if (file_exists($path)) {
                $content = @file_get_contents($path);
                if ($content && preg_match_all('/\/Count\s+(\d+)/', $content, $matches)) {
                    return (int) max($matches[1]);
                }
            }
        }

        return 1;
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    @if($isOpen)
        <div x-data="{
            pdfDoc: null,
            loading: false,
            pdfError: false,
            async initPdf() {
                const url = $wire.fileUrl;
                if (!url || $wire.fileType !== 'pdf') return;
                
                if (typeof window.pdfjsLib !== 'undefined') {
                    window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                }
                this.loading = true;
                this.pdfError = false;
                try {
                    const response = await fetch(url, { credentials: 'same-origin' });
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    const arrayBuffer = await response.arrayBuffer();
                    this.pdfDoc = await window.pdfjsLib.getDocument({ data: arrayBuffer }).promise;
                    $wire.set('totalPages', this.pdfDoc.numPages);
                    await this.renderPage($wire.currentPage);
                    await this.renderThumbnails();
                } catch (e) {
                    console.error('PDF.js loading error, using iframe fallback:', e);
                    this.pdfError = true;
                } finally {
                    this.loading = false;
                }
            },
            async renderPage(pageNum) {
                if (!this.pdfDoc) return;
                const page = await this.pdfDoc.getPage(pageNum || 1);
                const viewport = page.getViewport({ scale: 2.0 });
                const canvas = this.$refs.mainCanvas;
                if (!canvas) return;
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                await page.render({ canvasContext: context, viewport: viewport }).promise;
            },
            updateActiveThumbnail(pageNum) {
                const container = this.$refs.thumbContainer;
                if (!container) return;
                const thumbs = container.querySelectorAll('.doc-viewer-thumb');
                thumbs.forEach((thumb, index) => {
                    if (index + 1 === pageNum) {
                        thumb.classList.add('doc-viewer-thumb--active');
                        thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    } else {
                        thumb.classList.remove('doc-viewer-thumb--active');
                    }
                });
            },
            async renderThumbnails() {
                if (!this.pdfDoc) return;
                const container = this.$refs.thumbContainer;
                if (!container) return;
                container.innerHTML = '';
                for (let i = 1; i <= this.pdfDoc.numPages; i++) {
                    const page = await this.pdfDoc.getPage(i);
                    const viewport = page.getViewport({ scale: 0.25 });
                    
                    const card = document.createElement('div');
                    card.className = 'doc-viewer-thumb ' + (i === $wire.currentPage ? 'doc-viewer-thumb--active' : '');
                    card.setAttribute('role', 'button');
                    card.onclick = () => {
                        $wire.setPage(i);
                    };

                    const canvas = document.createElement('canvas');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    canvas.className = 'img-fluid rounded shadow-sm';
                    await page.render({ canvasContext: canvas.getContext('2d'), viewport: viewport }).promise;

                    const badge = document.createElement('span');
                    badge.className = 'thumb-num';
                    badge.innerText = i;

                    card.appendChild(canvas);
                    card.appendChild(badge);
                    container.appendChild(card);
                }
            }
        }" x-init="$watch('$wire.fileUrl', () => initPdf()); $watch('$wire.currentPage', (page) => { renderPage(page); updateActiveThumbnail(page); }); if ($wire.isOpen && $wire.fileUrl) initPdf();">
            {{-- Overlay Backdrop --}}
            <div wire:click="closeViewer" class="doc-viewer-backdrop"></div>

            {{-- Top Left Close Button --}}
            <button wire:click="closeViewer" type="button" class="doc-viewer-close-btn" title="Close Viewer">
                <i class="ph ph-x fs-2"></i>
            </button>

        {{-- ========================================================= --}}
        {{-- LEFT SIDEBAR: PAGE THUMBNAILS CONTAINER                   --}}
        {{-- Rendered ONLY for Multi-Page PDFs (totalPages > 1)        --}}
        {{-- Hidden for Single-Page PDFs & Images                      --}}
        {{-- ========================================================= --}}
            @if($fileType === 'pdf')
                <div class="doc-viewer-sidebar" x-ref="thumbContainer" x-show="totalPages > 1">
                </div>
            @endif

        {{-- ========================================================= --}}
        {{-- TOP CONTROL TOOLBAR                                       --}}
        {{-- ========================================================= --}}
            <div class="doc-viewer-toolbar {{ ($fileType === 'image' || $totalPages <= 1) ? 'doc-viewer-toolbar--collapsed-sidebar' : '' }}">
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

            {{-- FLOATING OVERLAY CHEVRONS --}}
            <button wire:click="{{ $fileType === 'image' ? 'prevImage' : 'prevPage' }}" type="button" class="doc-viewer-overlay-arrow doc-viewer-overlay-arrow--left" title="Previous">
                <i class="ph ph-caret-left fs-2 text-white"></i>
            </button>
            <button wire:click="{{ $fileType === 'image' ? 'nextImage' : 'nextPage' }}" type="button" class="doc-viewer-overlay-arrow doc-viewer-overlay-arrow--right" title="Next">
                <i class="ph ph-caret-right fs-2 text-white"></i>
            </button>

            {{-- MAIN CANVAS DISPLAY AREA --}}
            <div class="doc-viewer-canvas-wrapper {{ ($fileType === 'image' || $totalPages <= 1) ? 'doc-viewer-canvas-wrapper--full' : '' }}">
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
                                    <h5 class="fw-bold text-dark mb-1">{{ $title ?: 'Scanned Image' }}</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif($fileType === 'pdf' && $fileUrl)
                    <div class="doc-viewer-canvas h-100 w-100 overflow-auto text-center" style="transform: scale({{ $zoom / 100 }}); transform-origin: top center;">
                        <div class="doc-viewer-paper shadow-lg d-inline-block bg-white p-2 rounded" style="min-width: 600px; min-height: 700px;">
                            <canvas x-ref="mainCanvas" class="img-fluid rounded" x-show="!pdfError"></canvas>
                            <iframe x-show="pdfError" src="{{ $fileUrl }}#page={{ $currentPage }}" class="w-100 h-100 border-0" style="min-height: 700px;"></iframe>
                        </div>
                    </div>
                @else
                    <div class="doc-viewer-canvas text-center">
                        <div class="doc-viewer-paper shadow-lg">
                            <h5 class="fw-bold text-dark mb-1">{{ $title ?: 'Document' }}</h5>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
