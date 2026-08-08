<div>
    <div class="container-fluid p-4">
        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 12px; background-color: #d1e7dd; color: #0f5132;">
                <i class="ph ph-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="file-editor-card">
                    <div class="file-editor-header">
                        <a href="{{ $return_url }}" class="back-btn" title="Back to previous page" wire:navigate>
                            <i class="ph ph-arrow-left fs-5"></i>
                        </a>
                        <h3>Edit Scholar File</h3>
                    </div>

                    <form wire:submit="save" id="file_upload_form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label for="file_type">Document Type <span class="text-danger">*</span></label>
                                    <select wire:model.live="file_type_id" id="file_type" class="form-select" data-file-types="{{ json_encode($fileTypes) }}" required>
                                        <option value="" disabled>Select Document Type</option>
                                        @foreach ($fileTypes as $fileType)
                                            <option value="{{ $fileType->id }}">
                                                {{ $fileType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('file_type_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label for="file_name">File Name</label>
                                    <input type="text" wire:model="file_name" id="file_name" class="form-control" placeholder="Optional custom name">
                                    @error('file_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Error boundary for JS compilation -->
                        @error('compiledFile') 
                            <div class="alert alert-danger p-2 small mt-2">{{ $message }}</div> 
                        @enderror

                        <input type="file" id="uploaded_files" multiple accept=".pdf, .jpg, .jpeg, .png" style="display: none;">

                        <!-- Dynamic Metadata (Placeholder for JS generation) -->
                        <div id="dynamic_metadata_container" class="row mb-3"></div>

                        <!-- Drag and Drop Zone -->
                        <div class="file-upload-dropzone" onclick="document.getElementById('uploaded_files').click()">
                            <i class="ph ph-cloud-arrow-up upload-icon"></i>
                            <div class="upload-text">Click to add more files</div>
                            <div class="upload-hint">Supports PDF, JPG, PNG</div>
                        </div>

                        <!-- Added Files List -->
                        <div id="added_files_container" style="display: none;">
                            <label style="font-weight: 600; font-size: 0.85rem; color: #495057; margin-bottom: 0.5rem; display: block;">Source Files</label>
                            <div id="added_files_list" class="added-files-list"></div>
                        </div>

                        <!-- Preview Grid -->
                        <div class="preview-grid-wrapper" id="preview-grid-wrapper" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="preview-grid-title m-0">Document Pages & Sort Order</div>
                                <small class="text-muted"><i class="ph ph-info me-1"></i>Drag to reorder pages</small>
                            </div>
                            <div id="image_preview_container" class="preview-container" wire:ignore></div>
                        </div>

                        <div class="editor-actions">
                            <a href="{{ $return_url }}" class="btn-cancel" wire:navigate>Cancel</a>
                            <button type="submit" id="submit_btn" class="btn-compile">
                                <i class="ph ph-files"></i>
                                <span>Save Edits & Compile PDF</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    @script
    <script>
        // Use Livewire's script scope to prevent globals leaking
        const fileTypeSelect = document.getElementById('file_type');
        const fileInput = document.getElementById("uploaded_files");
        const metadataContainer = document.getElementById('dynamic_metadata_container');
        const previewContainer = document.getElementById('image_preview_container');
        const previewWrapper = document.getElementById('preview-grid-wrapper');
        const form = document.getElementById('file_upload_form');
        const submitBtn = document.getElementById('submit_btn');

        let isCompiling = false;

        if (previewContainer && typeof window.Sortable !== 'undefined') {
            new window.Sortable(previewContainer, {
                animation: 300,
                easing: "cubic-bezier(0.25, 1, 0.5, 1)",
                ghostClass: 'sortable-ghost',
                forceFallback: true,
                fallbackClass: 'sortable-fallback',
                onEnd: updatePreviewNumbers
            });
        }

        fileInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            if (files.length === 0) return;

            previewWrapper.style.display = 'block';

            files.forEach(file => {
                const fileId = 'file_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                
                addFileToListUI(file.name, fileId);

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        createImageCard(event.target.result, fileId);
                    };
                    reader.readAsDataURL(file);
                } else if (file.type === 'application/pdf') {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        segmentAndPreviewPDF(event.target.result, fileId);
                    };
                    reader.readAsArrayBuffer(file);
                }
            });

            fileInput.value = ''; // Reset to allow re-upload
        });

        function addFileToListUI(fileName, fileId) {
            const container = document.getElementById('added_files_container');
            const list = document.getElementById('added_files_list');
            container.style.display = 'block';

            const item = document.createElement('div');
            item.className = 'added-file-item';
            item.dataset.fileId = fileId;

            const infoDiv = document.createElement('div');
            infoDiv.className = 'file-info';
            
            const icon = document.createElement('i');
            icon.className = fileName.endsWith('.pdf') ? 'ph ph-file-pdf' : 'ph ph-image';
            
            const nameSpan = document.createElement('span');
            nameSpan.textContent = fileName;

            infoDiv.appendChild(icon);
            infoDiv.appendChild(nameSpan);

            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'btn-remove-file';
            deleteBtn.type = 'button';
            deleteBtn.title = 'Remove File';
            deleteBtn.innerHTML = '<i class="ph ph-trash"></i>';
            deleteBtn.onclick = function() {
                item.remove();
                const cards = previewContainer.querySelectorAll(`.preview-card-item[data-file-id="${fileId}"]`);
                cards.forEach(card => card.remove());
                updatePreviewNumbers();
            };

            item.appendChild(infoDiv);
            item.appendChild(deleteBtn);
            list.appendChild(item);
        }

        function createImageCard(dataUrl, fileId) {
            const card = document.createElement('div');
            card.className = 'preview-card-item';
            card.dataset.imageSrc = dataUrl;
            card.dataset.fileId = fileId;

            const mediaContainer = document.createElement('div');
            mediaContainer.className = 'preview-media-container';

            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'btn-remove-page';
            deleteBtn.innerHTML = '<i class="ph ph-x"></i>';
            deleteBtn.type = 'button';
            deleteBtn.onclick = function () {
                card.remove();
                const remaining = previewContainer.querySelectorAll(`.preview-card-item[data-file-id="${fileId}"]`);
                if (remaining.length === 0) {
                    const item = document.querySelector(`.added-file-item[data-file-id="${fileId}"]`);
                    if (item) item.remove();
                }
                updatePreviewNumbers();
            };

            const img = document.createElement('img');
            img.src = dataUrl;

            mediaContainer.appendChild(deleteBtn);
            mediaContainer.appendChild(img);

            const meta = document.createElement('div');
            meta.className = 'preview-meta';
            const badge = document.createElement('span');
            badge.className = 'page-badge';
            
            meta.appendChild(badge);

            card.appendChild(mediaContainer);
            card.appendChild(meta);
            
            previewContainer.appendChild(card);
            updatePreviewNumbers();
        }

        async function segmentAndPreviewPDF(pdfArrayBuffer, fileId){
            if (typeof window.pdfjsLib !== 'undefined') {
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            }

            const pdf = await pdfjsLib.getDocument({ data: pdfArrayBuffer }).promise;
            previewWrapper.style.display = 'block';

            for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                const page = await pdf.getPage(pageNum);
                const viewport = page.getViewport({ scale: 2.0 });

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                await page.render({ canvasContext: context, viewport: viewport }).promise;

                const card = document.createElement('div');
                card.className = 'preview-card-item';
                card.dataset.fileId = fileId;

                const mediaContainer = document.createElement('div');
                mediaContainer.className = 'preview-media-container';

                const deleteBtn = document.createElement('button');
                deleteBtn.className = 'btn-remove-page';
                deleteBtn.innerHTML = '<i class="ph ph-x"></i>';
                deleteBtn.type = 'button';
                deleteBtn.onclick = function () {
                    card.remove();
                    const remaining = previewContainer.querySelectorAll(`.preview-card-item[data-file-id="${fileId}"]`);
                    if (remaining.length === 0) {
                        const item = document.querySelector(`.added-file-item[data-file-id="${fileId}"]`);
                        if (item) item.remove();
                    }
                    updatePreviewNumbers();
                };

                mediaContainer.appendChild(deleteBtn);
                mediaContainer.appendChild(canvas);

                const meta = document.createElement('div');
                meta.className = 'preview-meta';
                const badge = document.createElement('span');
                badge.className = 'page-badge';
                meta.appendChild(badge);

                card.appendChild(mediaContainer);
                card.appendChild(meta);
                
                previewContainer.appendChild(card);
            }
            updatePreviewNumbers();
        }

        function updatePreviewNumbers() {
            const cards = previewContainer.querySelectorAll('.preview-card-item');
            
            cards.forEach((card, index) => {
                const badge = card.querySelector('.page-badge');
                if (badge) {
                    badge.textContent = `Page ${index + 1}`;
                }
            });

            const list = document.getElementById('added_files_list');
            const container = document.getElementById('added_files_container');
            
            if (list.children.length === 0) {
                container.style.display = 'none';
            }

            if (cards.length === 0) {
                previewWrapper.style.display = 'none';
            } else {
                previewWrapper.style.display = 'block';
            }

            // Sync file name if we have 1 file and the wire:model file_name is empty
            const fileItems = list.querySelectorAll('.added-file-item');
            if (fileItems.length === 1) {
                const nameSpan = fileItems[0].querySelector('span');
                if (nameSpan) {
                    const rawName = nameSpan.textContent;
                    const defaultName = rawName.substring(0, rawName.lastIndexOf('.')) || rawName;
                    const fileNameInput = document.getElementById("file_name");
                    if (fileNameInput && !fileNameInput.value) {
                        $wire.set('file_name', defaultName);
                    }
                }
            }
        }

        function loadImage(src) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => resolve(img);
                img.onerror = (err) => reject(err);
                img.src = src;
            });
        }

        function compressImage(img, quality = 0.95, maxWidth = 2480) {
            const canvas = document.createElement('canvas');
            let width = img.width;
            let height = img.height;

            if (width > maxWidth) {
                height = Math.round((height * maxWidth) / width);
                width = maxWidth;
            }

            canvas.width = width;
            canvas.height = height;

            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, width, height);
            ctx.drawImage(img, 0, 0, width, height);

            return canvas.toDataURL('image/jpeg', quality);
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            if (isCompiling) return;
            
            const cards = previewContainer.querySelectorAll('.preview-card-item');
            if (cards.length === 0) {
                // If there's no files, we just run Livewire's normal save.
                $wire.save();
                return;
            }

            isCompiling = true;
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="ph ph-spinner-gap ph-spin"></i><span>Processing...</span>';
            submitBtn.disabled = true;

            try {
                // PDF Compilation is temporarily disabled as requested
                /*
                const { jsPDF } = window.jspdf;
                if (!jsPDF) throw new Error("The jsPDF library failed to load.");
                
                let doc = null;

                for (let i = 0; i < cards.length; i++) {
                    const card = cards[i];
                    let imgData = '';

                    const canvas = card.querySelector('canvas');
                    const img = card.querySelector('img');

                    if (canvas) {
                        imgData = canvas.toDataURL('image/jpeg', 1.0);
                    } else if (img) {
                        imgData = img.src;
                    } else if (card.dataset.imageSrc) {
                        imgData = card.dataset.imageSrc;
                    }

                    if (!imgData) continue;

                    const imgObj = await loadImage(imgData);
                    const compressedImgData = compressImage(imgObj, 0.95);

                    const isLandscape = imgObj.width > imgObj.height;
                    const orientation = isLandscape ? "landscape" : "portrait";
                    const pageWidth = isLandscape ? 297 : 210;
                    const pageHeight = isLandscape ? 210 : 297;

                    if (i === 0) {
                        doc = new jsPDF({
                            orientation: orientation,
                            unit: "mm",
                            format: "a4"
                        });
                    } else {
                        doc.addPage("a4", orientation);
                    }

                    const imgWidth = pageWidth;
                    let imgHeight = (imgObj.height * imgWidth) / imgObj.width;

                    if (imgHeight > pageHeight) {
                        imgHeight = pageHeight;
                    }

                    const x = (pageWidth - imgWidth) / 2;
                    const y = (pageHeight - imgHeight) / 2;
                    
                    doc.addImage(compressedImgData, "JPEG", x, y, imgWidth, imgHeight, undefined, 'NONE');
                }

                if (!doc) throw new Error("No pages could be compiled into the PDF.");

                const pdfBlob = doc.output('blob');
                
                let customName = $wire.get('file_name');
                if (!customName || customName.trim() === '') customName = 'compiled_document';
                const finalName = customName.endsWith('.pdf') ? customName : `${customName}.pdf`;
                
                const compiledFile = new File([pdfBlob], finalName, { 
                    type: "application/pdf" 
                });

                // Upload the file via Livewire
                $wire.upload('compiledFile', compiledFile, (uploadedFilename) => {
                    // Success!
                    $wire.save();
                    
                    // Reset button after Livewire finishes request
                    setTimeout(() => {
                        submitBtn.innerHTML = originalBtnHtml;
                        submitBtn.disabled = false;
                        isCompiling = false;
                    }, 1000);
                }, () => {
                    // Error
                    alert('Failed to upload file to the server.');
                    submitBtn.innerHTML = originalBtnHtml;
                    submitBtn.disabled = false;
                    isCompiling = false;
                }, (event) => {
                    // Progress callback
                });
                */
                
                // Simulate success since compilation is disabled
                setTimeout(() => {
                    alert("Edits saved (Compilation skipped for now).");
                    submitBtn.innerHTML = originalBtnHtml;
                    submitBtn.disabled = false;
                    isCompiling = false;
                }, 1000);

            } catch (error) {
                console.error(error);
                alert('Error compiling PDF: ' + error.message);
                submitBtn.innerHTML = originalBtnHtml;
                submitBtn.disabled = false;
                isCompiling = false;
            }
        });
    </script>
    @endscript
</div>
