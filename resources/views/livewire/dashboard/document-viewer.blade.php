<?php

use Livewire\Attributes\On;
use Livewire\Volt\Component;
use App\Models\Document;

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
    public string|int $documentId = '';

    #[On('open-document-viewer')]
    public function openViewer(
        string $title = 'Amendatory Agreement',
        string $fileUrl = '',
        string $scholarName = '',
        ?string $fileType = null,
        int $documentIndex = 1,
        int $scholarId = 1,
        string|int $documentId = '',
    ): void {
        $this->title = $title;
        $this->fileUrl = $fileUrl;
        $this->scholarName = $scholarName;
        $this->documentIndex = $documentIndex;
        $this->scholarId = $scholarId;
        $this->documentId = $documentId;

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

        $doc = \App\Models\Document::with('currentVersion')
            ->where('uuid', $this->documentId)
            ->first();
    
        $filePath = $doc?->file_path;

        if ($filePath && \Illuminate\Support\Facades\Storage::disk('local')->exists($filePath)) {
            $path = \Illuminate\Support\Facades\Storage::disk('local')->path($filePath);
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

    public function zoomIn(): void
    {
        if ($this->zoom < 300) {
            $this->zoom += 25;
            $this->dispatch('zoom-changed', zoom: $this->zoom, rotation: $this->rotation);
        }
    }

    public function zoomOut(): void
    {
        if ($this->zoom > 25) {
            $this->zoom -= 25;
            $this->dispatch('zoom-changed', zoom: $this->zoom, rotation: $this->rotation);
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
        $this->redirect(route('scholars.files.edit', [
            'scholar'    => $this->scholarId,
            'document'       => $this->documentId,
            'return_url' => $returnUrl,
        ]));
    }

    public function deleteDocument(): void
    {
        $document = Document::findOrFail($this->documentId);
        $document->delete();
        $this->closeViewer();
        $this->dispatch('document-deleted', documentId: $this->documentId);
    }
}; ?>

<div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    @if($isOpen)
        <div>
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
                <div class="doc-viewer-sidebar" id="docViewerThumbContainer" wire:ignore>
                </div>
            @endif

        {{-- ========================================================= --}}
        {{-- TOP CONTROL TOOLBAR                                       --}}
        {{-- ========================================================= --}}
            <div class="doc-viewer-toolbar {{ ($fileType === 'image' || $totalPages <= 1) ? 'doc-viewer-toolbar--collapsed-sidebar' : '' }}">
                @if($fileType === 'pdf')
                    <span id="docViewerPageDisplay" class="small fw-semibold text-secondary me-2">Page {{ $currentPage }} / {{ $totalPages }}</span>
                    <div class="doc-viewer-toolbar__divider me-1"></div>
                @endif

                {{-- Zoom Controls --}}
                <div class="doc-viewer-toolbar__group me-1">
                    <button wire:click="zoomOut" type="button" class="btn btn-link text-dark p-0 border-0 shadow-none me-1" title="Zoom Out (-20%)">
                        <i class="ph ph-minus fs-6"></i>
                    </button>
                    <span class="small fw-semibold me-1">{{ $zoom }}%</span>
                    <button wire:click="zoomIn" type="button" class="btn btn-link text-dark p-0 border-0 shadow-none" title="Zoom In (+20%)">
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
                <button wire:click="deleteDocument" wire:confirm="Are you sure you want to delete this document? This cannot be undone." type="button" class="btn btn-link text-danger p-1 border-0 shadow-none" title="Delete">
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
                        <div class="doc-viewer-image-paper shadow-lg" style="overflow: auto;">
                            @if($fileUrl)
                                <img id="docViewerImage" src="{{ $fileUrl }}" alt="{{ $title }}" class="img-fluid" style="transform: scale({{ $zoom / 100 }}) rotate({{ $rotation }}deg); transform-origin: center center; transition: transform 0.2s ease;">
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
                    <div class="doc-viewer-canvas h-100 w-100 text-center">
                        <div id="docViewerPaperContainer" wire:ignore oncontextmenu="return false;" class="doc-viewer-paper shadow-lg bg-white rounded p-4 mx-auto" style="user-select: none; -webkit-user-select: none;">
                            <div id="docViewerPaperContent" style="transform: scale({{ $zoom / 100 }}); transform-origin: top center; transition: transform 0.2s ease; width: 100%;">
                                <div id="docViewerLoading" class="py-5 text-muted">
                                    <div class="spinner-border text-primary mb-2" role="status"></div>
                                    <p>Loading document pages...</p>
                                </div>
                            </div>
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
    
<!-- Assets for the document viewer -->
@assets
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
@endassets

<!-- Script to handle the scripting -->
@script
    <script>
        let pdfDocInstance = null;
        let currentRenderTaskId = 0;

        // A listener that waits for the element to render in the DOM using MutationObserver & fallback polling.
        function waitForElement(selector, maxTries = 200, interval = 50) {
            return new Promise((resolve) => {
                const existing = document.querySelector(selector);
                if (existing) {
                    return resolve(existing);
                }

                let count = 0;
                let timer = null;

                const cleanup = () => {
                    if (observer) observer.disconnect();
                    if (timer) clearInterval(timer);
                };

                const observer = new MutationObserver(() => {
                    const el = document.querySelector(selector);
                    if (el) {
                        cleanup();
                        resolve(el);
                    }
                });

                observer.observe(document.body, { childList: true, subtree: true });

                timer = setInterval(() => {
                    const el = document.querySelector(selector);
                    if (el) {
                        cleanup();
                        resolve(el);
                    } else if (++count >= maxTries) {
                        cleanup();
                        resolve(null);
                    }
                }, interval);
            });
        }

        /**
         * A method that handles loading the PDF and rendering each of its pages to the paper container.
         * 
         * It works as such:
         * 1. Loads the file from the server via a fetch request.
         * 2. Saves the file as an arrayBuffered PDF.
         * 3. Sets up a new render task to prevent race conditions from occurring when the user quickly navigates through multiple documents. 
         * 4. Iterates through all the pages and renders them to the paper container.
         * 5. Renders the thumbnails if the pages are greater than 1.
         * 6. Instantiates the scroller observer.
         */
        async function renderPdfDocument(targetUrl) {
            const url = targetUrl || $wire.fileUrl;
            if (!url){
                console.error('Document doesn\'t exist.');
                return;
            }

            // Method to prevent asynchronous rendering to display files that were clicked previously.
            const taskId = ++currentRenderTaskId;

            if (typeof window.pdfjsLib !== 'undefined') {
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            }

            if (taskId !== currentRenderTaskId) return;
            const paperContainer = await waitForElement('#docViewerPaperContainer', 200);
            const thumbContainer = document.getElementById('docViewerThumbContainer');
            if (!paperContainer || taskId !== currentRenderTaskId) {
                if (taskId === currentRenderTaskId) {
                    console.error('Paper container doesn\'t exist in DOM.');
                }
                return;
            }

            // Show loading spinner
            paperContainer.innerHTML = `
                <div id="docViewerLoading" class="py-5 text-muted text-center">
                    <div class="spinner-border text-primary mb-2" role="status"></div>
                    <p>Loading document pages...</p>
                </div>
            `;

            fetch(url, { credentials: 'same-origin' })
                .then(response => {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.arrayBuffer();
                })
                .then(arrayBuffer => {
                    if (taskId !== currentRenderTaskId) return null;
                    return window.pdfjsLib.getDocument({ data: arrayBuffer }).promise;
                })
                .then(async (pdf) => {
                    if (!pdf || taskId !== currentRenderTaskId) return;

                    pdfDocInstance = pdf;
                    console.log('Gotten PDF document instance');
                    
                    const totalPages = pdfDocInstance.numPages;
                    $wire.set('totalPages', totalPages);

                    const targetContent = document.getElementById('docViewerPaperContent') || paperContainer;
                    targetContent.innerHTML = '';
                    for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                        if (taskId !== currentRenderTaskId) return;

                        const page = await pdfDocInstance.getPage(pageNum);
                        const viewport = page.getViewport({ scale: 2.0 });

                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        await page.render({ canvasContext: context, viewport: viewport }).promise;

                        if (taskId !== currentRenderTaskId) return;

                        const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
                        const blobUrl = URL.createObjectURL(blob);

                        const img = document.createElement('img');
                        img.src = blobUrl;
                        img.alt = `Page ${pageNum}`;
                        img.className = 'img-fluid mb-3 rounded shadow-sm d-block mx-auto';
                        img.style.width = `${$wire.zoom}%`;
                        img.style.maxWidth = $wire.zoom > 100 ? 'none' : '100%';
                        img.style.transition = 'width 0.2s ease';
                        img.style.userSelect = 'none';
                        img.style.webkitUserSelect = 'none';
                        img.style.webkitUserDrag = 'none';
                        img.setAttribute('draggable', 'false');
                        img.oncontextmenu = (e) => e.preventDefault();
                        img.ondragstart = (e) => e.preventDefault();
                        img.id = `pdf-page-${pageNum}`;
                        img.dataset.pageNum = pageNum;

                        targetContent.appendChild(img);
                    }

                    if (thumbContainer && taskId === currentRenderTaskId) {
                        renderPdfThumbnails(thumbContainer);
                    }

                    if (taskId === currentRenderTaskId) {
                        setupPageScrollObserver();
                    }
                })
                .catch(e => {
                    if (taskId === currentRenderTaskId) {
                        console.error('PDF.js loading error, falling back to iframe:', e);
                        paperContainer.innerHTML = `<iframe src="${url}#page=1" class="w-100 h-100 border-0 rounded" style="min-height: 600px;"></iframe>`;
                    }
                });
        }

        let pageObserver = null;

        /**
         * Automatically updates the span element that displays the "currentPage / totalPages" in the thumb toolbar
         * 
         * 1. Removes other existing pageObservers rendered from the previous files. (Preventing memory leaks.)
         * 2. Instantiates a new IntersectionObserver that handles the updating of the page counter.
         * 3. Iterates through all the page images and adds them to the IntersectionObserver.
         */
        function setupPageScrollObserver() {
            if (pageObserver) {
                pageObserver.disconnect();
            }

            const paperContainer = document.getElementById('docViewerPaperContainer');
            if (!paperContainer) return;

            // "entries" are the pages whose visibility has changed (if seen or hidden from the viewport, i.e., can be seen in the paper container or was already scroll passed.)
            pageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    // If a new entry (i.e., page) is now seen (i.e. intersecting), update the current page display in the thumb toolbar.
                    if (entry.isIntersecting) {
                        const pageNum = parseInt(entry.target.dataset.pageNum);
                        if (pageNum) {
                            updateCurrentPageDisplay(pageNum);
                        }
                    }
                });
            }, {
                // Requires 40% of the image to be seen before it is considered as "seen" or intersecting.
                root: paperContainer,
                threshold: 0.4
            });

            // Gets all rendered images and add the IntersectionObserver over it.
            const pageImgs = paperContainer.querySelectorAll('img[id^="pdf-page-"]');
            pageImgs.forEach(img => pageObserver.observe(img));
        }

        // Method to update the page display in the thumb toolbar.
        function updateCurrentPageDisplay(pageNum) {
            const pageDisplay = document.getElementById('docViewerPageDisplay');
            if (pageDisplay) {
                pageDisplay.innerText = `Page ${pageNum} / ${$wire.totalPages}`;
            }

            const thumbContainer = document.getElementById('docViewerThumbContainer');
            if (thumbContainer) {
                const thumbs = thumbContainer.querySelectorAll('.doc-viewer-thumb');
                thumbs.forEach((thumb, idx) => {
                    if (idx + 1 === pageNum) {
                        thumb.classList.add('doc-viewer-thumb--active');
                    } else {
                        thumb.classList.remove('doc-viewer-thumb--active');
                    }
                });
            }

            // Renders the change via a livewire method call.
            $wire.set('currentPage', pageNum, false);
        }

        // Method to render the thumbnails.
        // The logic is similar to the pdf pages but is controlled by the css design.
        async function renderPdfThumbnails(container) {
            if (!pdfDocInstance || !container) return;

            // Displays the thumbnails if the total number of pages is greater than 1.
            if (pdfDocInstance.numPages > 1) {
                container.style.display = 'flex';
            } else {
                container.style.display = 'none';
                return;
            }

            container.innerHTML = '';
            for (let i = 1; i <= pdfDocInstance.numPages; i++) {
                const page = await pdfDocInstance.getPage(i);
                const viewport = page.getViewport({ scale: 0.25 });
                
                const card = document.createElement('div');
                card.className = 'doc-viewer-thumb ' + (i === $wire.currentPage ? 'doc-viewer-thumb--active' : '');
                card.setAttribute('role', 'button');
                card.onclick = () => {
                    if (container) {
                        container.querySelectorAll('.doc-viewer-thumb').forEach(el => el.classList.remove('doc-viewer-thumb--active'));
                    }
                    card.classList.add('doc-viewer-thumb--active');
                    updateCurrentPageDisplay(i);
                    scrollToPdfPage(i);
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

        // Method to scroll to that specific page once a thumbnail is clicked.
        function scrollToPdfPage(pageNum) {
            const pageImg = document.getElementById(`pdf-page-${pageNum}`);
            if (pageImg) {
                pageImg.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // Listener for opening the document viewer
        Livewire.on('open-document-viewer', (event) => {
            const payload = Array.isArray(event) ? event[0] : event;
            const targetUrl = payload?.fileUrl || $wire.fileUrl;
            setTimeout(() => renderPdfDocument(targetUrl), 200);
        });

        // Listener for triggering browser printing via hidden iframe
        Livewire.on('print-requested', (event) => {
            const payload = Array.isArray(event) ? event[0] : event;
            const url = payload?.url || $wire.fileUrl;
            if (!url) return;

            let printIframe = document.getElementById('docViewerPrintIframe');
            if (!printIframe) {
                printIframe = document.createElement('iframe');
                printIframe.id = 'docViewerPrintIframe';
                printIframe.style.display = 'none';
                document.body.appendChild(printIframe);
            }

            printIframe.src = url;
            printIframe.onload = () => {
                setTimeout(() => {
                    printIframe.contentWindow.focus();
                    printIframe.contentWindow.print();
                }, 150);
            };
        });

        // Listener for triggering document file download
        Livewire.on('download-requested', (event) => {
            const payload = Array.isArray(event) ? event[0] : event;
            const url = payload?.url || $wire.fileUrl;
            if (!url) return;

            const downloadUrl = url.includes('/view') ? url.replace('/view', '/download') : url;
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.setAttribute('download', '');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        // Listener for reactive zoom changes on rendered page images & image elements
        Livewire.on('zoom-changed', (event) => {
            const payload = Array.isArray(event) ? event[0] : event;
            const zoomLevel = payload?.zoom || $wire.zoom;
            const rotation = payload?.rotation ?? ($wire.rotation || 0);

            // 1. PDF Page Images
            const paperContainer = document.getElementById('docViewerPaperContainer');
            if (paperContainer) {
                const pageImgs = paperContainer.querySelectorAll('img[id^="pdf-page-"]');
                pageImgs.forEach(img => {
                    img.style.width = `${zoomLevel}%`;
                    img.style.maxWidth = zoomLevel > 100 ? 'none' : '100%';
                    img.style.transition = 'width 0.2s ease';
                });
            }

            // 2. PDF Paper Content Scale
            const paperContent = document.getElementById('docViewerPaperContent');
            if (paperContent) {
                paperContent.style.transform = `scale(${zoomLevel / 100})`;
                paperContent.style.transformOrigin = 'top center';
                paperContent.style.transition = 'transform 0.2s ease';
            }

            // 3. Image Mode Viewer
            const viewerImg = document.getElementById('docViewerImage') || document.querySelector('.doc-viewer-image-container img');
            if (viewerImg) {
                viewerImg.style.transform = `scale(${zoomLevel / 100}) rotate(${rotation}deg)`;
                viewerImg.style.transformOrigin = 'center center';
                viewerImg.style.transition = 'transform 0.2s ease';
            }
        });
    </script>
@endscript
</div>
