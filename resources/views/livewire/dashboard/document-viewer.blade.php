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
            <div class="doc-viewer-paper shadow-lg">
                {{-- Document Header --}}
                <div class="text-center mb-4">
                    <img src="{{ asset('DostSEILogo.svg') }}" alt="Logo" style="height: 3.5rem; width: auto;" class="mb-2">
                    <h6 class="fw-bold text-dark mb-0 fs-6">University of Southeastern Philippines</h6>
                    <span class="small text-secondary">OBRERO CAMPUS</span>
                    <h5 class="fw-bold text-dark mt-3 mb-0" style="font-size: 1.1rem; letter-spacing: 0.05em;">REPORT OF GRADES</h5>
                    <span class="small text-muted">2026 Summer</span>
                </div>

                {{-- Student Info Header --}}
                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 mb-4 border">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-secondary bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 3.25rem; height: 3.25rem;">
                            <i class="ph ph-user fs-3 text-secondary"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.9rem;">FERNANDEZ, Gianfranco Miguel D.</h6>
                            <div class="small text-muted" style="font-size: 0.78rem;">ID Number: <strong class="text-dark">2023-00855</strong></div>
                            <div class="small text-muted" style="font-size: 0.78rem;">Course: <strong class="text-dark">Bachelor of Science in Computer Science</strong></div>
                            <div class="small text-muted" style="font-size: 0.78rem;">Year Level: <strong class="text-dark">3rd Year</strong></div>
                        </div>
                    </div>
                </div>

                {{-- Grades Table --}}
                <table class="table table-bordered table-sm align-middle small mb-4">
                    <thead class="table-dark text-uppercase fs-8">
                        <tr>
                            <th>Code</th>
                            <th>Subject Title</th>
                            <th class="text-center">Unit</th>
                            <th class="text-center">Final</th>
                            <th class="text-center">Re-Exam</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold">Thesis 400</td>
                            <td>Thesis</td>
                            <td class="text-center">2.0</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-success fw-bold">* Enrolled</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">CSPR 331</td>
                            <td>CS Practicum</td>
                            <td class="text-center">3.0</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-success fw-bold">* Enrolled</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Footer Disclaimer & Date Stamp --}}
                <div class="d-flex justify-content-between align-items-end mt-4 pt-3 border-top">
                    <span class="badge bg-secondary bg-opacity-10 text-dark border px-2 py-1 small" style="font-size: 0.7rem;">
                        STUDENT PORTAL portal.usep.edu.ph
                    </span>
                    <span class="small text-muted italic" style="font-size: 0.7rem;">
                        *This is a system generated report. No signature required.
                        <br>Date Generated: Tue Jan 23 2026
                    </span>
                </div>
            </div>
        </div>
    @endif
</div>


