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
                        <a href="{{ $return_url . "?open_scholar=" . $scholar->id }}" class="back-btn" title="Back to previous page" wire:navigate>
                            <i class="ph ph-arrow-left fs-5"></i>
                        </a>
                        <h3>Add Scholar File</h3>
                    </div>

                    <form id="file_upload_form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label for="file_type">Document Type <span class="text-danger">*</span></label>
                                    <select wire:model="file_type_id" id="file_type" class="form-select" required>
                                        <option value="" disabled>Select Document Type</option>
                                        @foreach ($this->fileTypes as $fileType)
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
                        <div id="dynamic_metadata_container" class="row mb-3" wire:ignore></div>

                        <!-- Drag and Drop Zone -->
                        <div class="file-upload-dropzone position-relative" 
                             id="dropzone_files"
                             onclick="document.getElementById('uploaded_files').click()"
                             ondragover="event.preventDefault(); this.style.borderColor='var(--dost-main-blue)';"
                             ondragleave="this.style.borderColor='';"
                             ondrop="handleDrop(event)">
                            <i class="ph ph-cloud-arrow-up upload-icon"></i>
                            <div class="upload-text">Click or drag files to add more</div>
                            <div class="upload-hint">Supports PDF, JPG, PNG</div>
                        </div>

                        <!-- Added Files List -->
                        <div id="added_files_container" style="display: none;" wire:ignore>
                            <label style="font-weight: 600; font-size: 0.85rem; color: #495057; margin-bottom: 0.5rem; display: block;">Source Files</label>
                            <div id="added_files_list" class="added-files-list"></div>
                        </div>

                        <!-- Preview Grid -->
                        <div class="preview-grid-wrapper" id="preview-grid-wrapper" style="display: none;" wire:ignore>
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

    {{-- Prepares the packages used to split and create of PDFs --}}
    <script type='module' src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    @script
    <script type="module">
        // Use Livewire's script scope to prevent globals leaking
        const fileTypeSelect = document.getElementById('file_type');
        const fileInput = document.getElementById("uploaded_files");
        const metadataContainer = document.getElementById('dynamic_metadata_container');
        const previewContainer = document.getElementById('image_preview_container');
        const previewWrapper = document.getElementById('preview-grid-wrapper');
        const form = document.getElementById('file_upload_form');
        const submitBtn = document.getElementById('submit_btn');

        let isCompiling = false;

        // Code to prepare the sortable
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

        // Insert the metadata logic
        /**
         * Add a new div for the new metadata fields
         * Get the pre-existing div and prepare to append children to them
         * Load the necessary data following the art style and logic of the edit.blade.php
         * 
         * addEventListener('change', generateMetadataFields);
         */
        async function getMetadataFields(selectedTypeId = null){
            if (!metadataContainer) return;
            metadataContainer.innerHTML = '';

            const fileTypes = @js($this->fileTypes);
            const currentId = selectedTypeId ?? (fileTypeSelect ? fileTypeSelect.value : @js($file_type_id));
            const fileType = fileTypes.find(ft => ft.id == currentId);

            if (!fileType || !fileType['metadata_template']) {
                return;
            }

            let metadata = fileType['metadata_template'];
            if (typeof metadata === 'string') {
                try { metadata = JSON.parse(metadata); } catch(e) { metadata = []; }
            }

            if (!Array.isArray(metadata)) return;

            const existingMetadata = @js($metadata) || {};

            for (let i = 0; i < metadata.length; i++) {
                const field = metadata[i];
                if (!field || !field.field_name) continue;

                const prefill_data = existingMetadata[field.field_name];

                const wrapper = document.createElement('div');
                wrapper.className = 'col-md-6 form-group-custom';

                const label = document.createElement('label');
                label.htmlFor = 'meta_' + field.field_name;
                label.textContent = field.label || field.field_name;
                if (field.required !== false) {
                    const reqSpan = document.createElement('span');
                    reqSpan.className = 'text-danger ms-1';
                    reqSpan.textContent = '*';
                    label.appendChild(reqSpan);
                }
                wrapper.appendChild(label);

                const inputName = `metadata[${field.field_name}]`;
                
                if (field.datatype === 'enum' && Array.isArray(field.values)) {
                    const select = document.createElement('select');
                    select.name = inputName;
                    select.id = 'meta_' + field.field_name;
                    select.className = 'form-select';
                    if (field.required !== false) select.required = true;

                    const defaultOpt = document.createElement('option');
                    defaultOpt.value = '';
                    defaultOpt.disabled = true;
                    if (prefill_data == null || prefill_data === '') {
                        defaultOpt.selected = true;
                    }
                    defaultOpt.textContent = `Select ${field.label || 'option'}`;
                    select.appendChild(defaultOpt);

                    field.values.forEach(val => {
                        const opt = document.createElement('option');
                        opt.value = val;
                        opt.textContent = val;
                        if (prefill_data != null && String(val) === String(prefill_data)) {
                            opt.selected = true;
                        }
                        select.appendChild(opt);
                    });
                    wrapper.appendChild(select);
                } else if (field.foreign_key) {
                    const select = document.createElement('select');
                    select.name = inputName;
                    select.id = 'meta_' + field.field_name;
                    select.className = 'form-select';
                    if (field.required !== false) select.required = true;
                    
                    const loadingOpt = document.createElement('option');
                    loadingOpt.value = "";
                    loadingOpt.textContent = `Loading ${field.label}...`;
                    select.appendChild(loadingOpt);
                    wrapper.appendChild(select);

                    fetch(`/api/metadata-options/${field.foreign_key}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            select.innerHTML = '';
                            const defaultOpt = document.createElement('option');
                            defaultOpt.value = '';
                            defaultOpt.disabled = true;
                            if (prefill_data == null || prefill_data === '') {
                                defaultOpt.selected = true;
                            }
                            defaultOpt.textContent = `Select ${field.label || 'option'}`;
                            select.appendChild(defaultOpt);

                            data.forEach(item => {
                                const opt = document.createElement('option');
                                opt.value = item.id;
                                opt.textContent = item.name || `ID: ${item.id}`; 
                                if (prefill_data != null && item.id == prefill_data) {
                                    opt.selected = true;
                                }
                                select.appendChild(opt);
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching foreign key data:', error);
                            select.innerHTML = '<option value="" disabled>Error loading options</option>';
                        });
                } else {
                    const input = document.createElement('input');
                    input.name = inputName;
                    input.id = 'meta_' + field.field_name;
                    input.className = 'form-control';
                    if (field.required !== false) input.required = true;
                    input.placeholder = `Enter ${field.label || field.field_name}`;
                    
                    if (field.datatype === 'int' || field.datatype === 'integer' || field.datatype === 'number') {
                        input.type = 'number';
                    } else if (field.datatype === 'date') {
                        input.type = 'date';
                    } else {
                        input.type = 'text';
                    }
                    input.value = prefill_data ?? '';
                    wrapper.appendChild(input);
                }

                metadataContainer.appendChild(wrapper);
            }
        }

        // Initial render
        getMetadataFields(@js($file_type_id));

        // Listen for DOM select change
        if (fileTypeSelect) {
            fileTypeSelect.addEventListener('change', function(e) {
                getMetadataFields(e.target.value);
            });
        }

        // Listen for Livewire property changes
        if (typeof $wire !== 'undefined') {
            $wire.watch('file_type_id', (newVal) => {
                getMetadataFields(newVal);
            });
        }

        // Prevent default browser behavior on file drop
        window.addEventListener('dragover', function(e) {
            e.preventDefault();
        }, false);

        window.addEventListener('drop', function(e) {
            e.preventDefault();
        }, false);

        // Handling the file and drop box logic
        function handleDrop(event) {
            event.preventDefault();
            event.stopPropagation();

            const dropzone = document.getElementById('dropzone_files');
            if (dropzone) {
                dropzone.style.borderColor = '';
            }

            const dt = event.dataTransfer;
            if (!dt || !dt.files || dt.files.length === 0) return;

            processFiles(Array.from(dt.files));
        }
        window.handleDrop = handleDrop;

        // Reads files and renders each page of a PDF or an image to the paper container
        function processFiles(files) {
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
                } else if (file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf')) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        segmentAndPreviewPDF(event.target.result, fileId);
                    };
                    reader.readAsArrayBuffer(file);
                }
            });
        }

        fileInput.addEventListener('change', function(e) {
            processFiles(Array.from(e.target.files));
            fileInput.value = ''; // Reset to allow re-upload
        });

        // Adds the file to the list with all its properties to the side bar
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

        // Function to generate the card for images in the paper container
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

        // Function to generate the card for PDFs in the paper container
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

        // Updates the page numbers in the paper container and the file list in the side bar
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

            // Function to gather metadata inputs from DOM
            const getMetadataFromDOM = () => {
                const metadataObj = {};
                if (metadataContainer) {
                    const metaInputs = metadataContainer.querySelectorAll('[name^="metadata["]');
                    metaInputs.forEach(input => {
                        const match = input.name.match(/^metadata\[(.+)\]$/);
                        if (match && match[1]) {
                            metadataObj[match[1]] = input.value;
                        }
                    });
                }
                return metadataObj;
            };

            if (cards.length === 0) {
                alert("A document must contain at least one page or file. Please add a file before saving.");
                return;
            }

            isCompiling = true;
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="ph ph-spinner-gap ph-spin"></i><span>Processing...</span>';
            submitBtn.disabled = true;

            try {
                // PDF Compilation
                const { jsPDF } = window.jspdf;
                if (!jsPDF) throw new Error("The jsPDF library failed to load.");
                
                let doc = null;

                for (let i = 0; i < cards.length; i++) {
                    const card = cards[i];
                    let imgData = '';

                    const canvas = card.querySelector('canvas');
                    const img = card.querySelector('img');

                    if (canvas) {
                        imgData = canvas.toDataURL('image/jpeg', .90);
                    } else if (img) {
                        imgData = img.src;
                    } else if (card.dataset.imageSrc) {
                        imgData = card.dataset.imageSrc;
                    }

                    if (!imgData) continue;

                    const imgObj = await loadImage(imgData);
                    const compressedImgData = compressImage(imgObj, 1);

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
                
                // Creates a destined URL to view the file, debugging whether the PDF is being compiled properly
                // const blobUrl = URL.createObjectURL(pdfBlob);
                
                // Open compiled PDF blob preview in a new browser tab for inspection
                // window.open(blobUrl, '_blank');
                
                let customName = $wire.get('file_name');
                if (!customName || customName.trim() === '') customName = 'compiled_document';
                const finalName = customName.endsWith('.pdf') ? customName : `${customName}.pdf`;

                // Sync metadata to Livewire client-side property
                $wire.metadata = getMetadataFromDOM();

                // Convert PDF Blob to Base64 data URL and send directly to Livewire backend
                const reader = new FileReader();
                reader.readAsDataURL(pdfBlob);
                reader.onloadend = async function() {
                    try {
                        const base64Data = reader.result;
                        console.log('Sending compiled PDF base64 payload to server...', `size: ${pdfBlob.size}`);
                        await $wire.saveCompiledPdf(base64Data, finalName);
                    } catch (err) {
                        console.error('Save error:', err);
                        alert('Error saving compiled PDF: ' + (err.message || err));
                        submitBtn.innerHTML = originalBtnHtml;
                        submitBtn.disabled = false;
                        isCompiling = false;
                    }
                };
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
