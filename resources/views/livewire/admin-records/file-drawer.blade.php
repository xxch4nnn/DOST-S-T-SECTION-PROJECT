<div>
    {{-- Drawer Backdrop Overlay --}}
    @if($isOpen)
        <div class="scholar-drawer-backdrop" wire:click="closeDrawer"></div>
    @endif

    {{-- Slide-over Drawer Panel --}}
    <div class="scholar-drawer {{ $isOpen ? 'scholar-drawer--open' : '' }}">
        {{-- Transparent Top Bar with Curved White Action Tab --}}
        <div class="scholar-drawer__top-bar">
            <div class="scholar-drawer__action-tab">
                {{-- Download Icon --}}
                <button type="button" class="btn btn-link text-dark p-1" title="Download Document">
                    <i class="ph ph-download-simple fs-5"></i>
                </button>

                {{-- Print Icon --}}
                <button type="button" class="btn btn-link text-dark p-1" title="Print Document">
                    <i class="ph ph-printer fs-5"></i>
                </button>

                {{-- 3-Dot Action Menu Dropdown --}}
                <div class="scholar-action-dropdown-wrapper">
                    <button 
                        type="button" 
                        class="btn btn-link text-dark p-1" 
                        title="More Actions"
                        wire:click.stop="toggleActionMenu"
                    >
                        <i class="ph ph-dots-three-vertical fs-5"></i>
                    </button>

                    @if($showActionMenu)
                        <div class="scholar-action-dropdown">
                            <button type="button" class="dropdown-item-btn dropdown-item-btn--danger" wire:click="closeDrawer">
                                <i class="ph ph-x-circle"></i>
                                <span>Close Drawer</span>
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Close X Icon --}}
                <button type="button" class="btn btn-link text-dark p-1 ms-1" wire:click="closeDrawer" title="Close">
                    <i class="ph ph-x fs-5"></i>
                </button>
            </div>
        </div>

        {{-- Main White Card Body --}}
        <div class="scholar-drawer__white-card">
            @if($record)
                {{-- Record Title --}}
                <div class="d-flex align-items-start justify-content-between mb-1">
                    <h2 class="scholar-drawer__title">{{ $record->title }}</h2>
                </div>

                <div class="scholar-drawer__spas mb-4">
                    Reference Series No: <strong>{{ $record->series_number ?? 'AFR-2023-00855' }}</strong>
                </div>

                {{-- Metadata Grid --}}
                <div class="scholar-drawer__grid">
                    <div class="scholar-meta-item">
                        <label>Record Type / Category</label>
                        <value>{{ $record->record_type }}</value>
                    </div>

                    <div class="scholar-meta-item">
                        <label>Fiscal Year / Series</label>
                        <value>{{ $record->year }}</value>
                    </div>

                    <div class="scholar-meta-item">
                        <label>Recipient / Office</label>
                        <value>{{ $record->recipient }}</value>
                    </div>

                    <div class="scholar-meta-item">
                        <label>Date Logged / Created</label>
                        <value>{{ $record->created_at ? $record->created_at->format('Y-m-d') : '' }}</value>
                    </div>
                </div>

                {{-- Scanned Documents Section --}}
                <div class="scholar-drawer__scanned-section">
                    <div class="folder-accordion">
                        <div class="folder-tab-btn" wire:click="$set('activeFolderTab', 'documents')">
                            <span>Scanned File Documents</span>
                        </div>

                        <div class="folder-accordion__content">
                            <div class="d-flex gap-3 overflow-x-auto py-1">
                                {{-- Thumbnail 1 --}}
                                <div class="doc-thumbnail-card" wire:click="openDocumentViewer">
                                    <div class="doc-mini-paper">
                                        <div class="doc-mini-header">
                                            <div class="doc-mini-logo"></div>
                                            <div class="doc-mini-title">OFFICIAL RECORD</div>
                                            <div class="doc-mini-sub">{{ $record->series_number }}</div>
                                        </div>
                                        <div class="doc-mini-body">
                                            <div class="doc-mini-line"></div>
                                            <div class="doc-mini-line short"></div>
                                            <div class="doc-mini-table"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Thumbnail 2 --}}
                                <div class="doc-thumbnail-card" wire:click="openDocumentViewer">
                                    <div class="doc-mini-paper">
                                        <div class="doc-mini-header">
                                            <div class="doc-mini-logo"></div>
                                            <div class="doc-mini-title">ATTACHMENT PAGE</div>
                                            <div class="doc-mini-sub">Page 2</div>
                                        </div>
                                        <div class="doc-mini-body">
                                            <div class="doc-mini-line"></div>
                                            <div class="doc-mini-line"></div>
                                            <div class="doc-mini-table"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
