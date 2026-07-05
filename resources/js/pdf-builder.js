import * as pdfjsLib from 'pdfjs-dist';

pdfjsLib.GlobalWorkerOptions.workerSrc = new URL(
    'pdfjs-dist/build/pdf.worker.min.mjs',
    import.meta.url,
).toString();

document.addEventListener('alpine:init', () => {
    window.Alpine.data('templateBuilder', () => ({
        fields: [],
        builder: null,
        currentPage: 1,
        numPages: 1,
        selectedFieldId: null,
        saving: false,
        init() {
            const canvas = this.$el.querySelector('#pdf-canvas');
            const overlay = this.$el.querySelector('#field-overlay');
            if (!canvas || !overlay) return;
            const pdfUrl = this.$el.getAttribute('data-pdf-url');
            if (!pdfUrl) return;
            const fieldsAttr = this.$el.getAttribute('data-fields');
            if (fieldsAttr) {
                try { this.fields = JSON.parse(fieldsAttr); } catch(e) {}
            }
            window.addEventListener('pdf-loaded', e => {
                this.numPages = e.detail.numPages;
            });
            
            // Watch changes to fields and selectedFieldId to redraw the canvas reactively
            this.$watch('fields', () => {
                if (this.builder) this.builder.drawFields();
            });
            this.$watch('selectedFieldId', () => {
                if (this.builder) this.builder.drawFields();
            });

            this.$nextTick(() => {
                this.builder = window.initPdfBuilder('pdf-canvas', 'field-overlay', pdfUrl, this);
            });
        },
        setFields(fields) {
            this.fields = fields;
        },
        updateField(id, data) {
            const idx = this.fields.findIndex(f => f.id === id);
            if (idx >= 0) {
                Object.assign(this.fields[idx], data);
                // Trigger watch by assigning array
                this.fields = [...this.fields];
            }
        },
        deleteField(id) {
            this.fields = this.fields.filter(f => f.id !== id);
            if (this.selectedFieldId === id) this.selectedFieldId = null;
        },
        selectField(id) {
            this.selectedFieldId = id;
        },
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                if (this.builder) this.builder.setPage(this.currentPage);
            }
        },
        nextPage() {
            if (this.currentPage < this.numPages) {
                this.currentPage++;
                if (this.builder) this.builder.setPage(this.currentPage);
            }
        },
        async saveMappings() {
            const valid = this.fields.filter(f => f.field_key);
            if (valid.length === 0) {
                if (window.toast) {
                    window.toast('Place at least one field on the template.', 'warning');
                } else {
                    alert('Place at least one field on the template.');
                }
                return;
            }
            this.saving = true;
            try {
                await this.$wire.call('saveFieldMappings', valid);
            } finally {
                this.saving = false;
            }
        },
    }));
});

function initPdfBuilder(canvasId, overlayId, pdfUrl, alpineComponent) {
    const canvas = document.getElementById(canvasId);
    const overlay = document.getElementById(overlayId);
    if (!canvas || !overlay) return null;

    overlay.style.display = 'block';

    const ctx = overlay.getContext('2d');
    let pageWidth = 0;
    let pageHeight = 0;
    let isDragging = false;
    let dragIndex = -1;
    let dragOffsetX = 0;
    let dragOffsetY = 0;
    let isResizing = false;
    let nextId = alpineComponent.fields.length > 0 ? Math.max(...alpineComponent.fields.map(f => f.id || 0)) + 1 : 1;

    const fieldColors = [
        'rgba(99, 102, 241, 0.35)', 'rgba(239, 68, 68, 0.35)',
        'rgba(34, 197, 94, 0.35)', 'rgba(234, 179, 8, 0.35)', 'rgba(168, 85, 247, 0.35)',
    ];

    let currentPage = 1;
    let pdfDoc = null;

    let initData;
    if (pdfUrl.startsWith('JVBERi')) {
        const binaryString = atob(pdfUrl);
        const len = binaryString.length;
        const bytes = new Uint8Array(len);
        for (let i = 0; i < len; i++) {
            bytes[i] = binaryString.charCodeAt(i);
        }
        initData = { data: bytes };
    } else {
        initData = { url: pdfUrl };
    }

    pdfjsLib.getDocument(initData).promise.then(pdf => {
        pdfDoc = pdf;
        window.dispatchEvent(new CustomEvent('pdf-loaded', { detail: { numPages: pdf.numPages } }));
        loadPage(currentPage);
    });

    function loadPage(pageNum) {
        if (!pdfDoc) return;
        currentPage = pageNum;
        pdfDoc.getPage(pageNum).then(page => {
            const viewport = page.getViewport({ scale: 1.5 });
            pageWidth = viewport.width;
            pageHeight = viewport.height;

            canvas.width = pageWidth;
            canvas.height = pageHeight;
            overlay.width = pageWidth;
            overlay.height = pageHeight;

            const renderCtx = canvas.getContext('2d');
            page.render({ canvasContext: renderCtx, viewport }).promise.then(() => drawFields());
        });
    }

    function drawFields() {
        ctx.clearRect(0, 0, overlay.width, overlay.height);
        const rect = overlay.getBoundingClientRect();
        const handleSize = 8 * (overlay.width / (rect.width || overlay.width));

        alpineComponent.fields.forEach((field, i) => {
            const fieldPage = field.page_number || 1;
            if (fieldPage !== currentPage) return;
            const color = fieldColors[i % fieldColors.length];
            const isSelected = field.id === alpineComponent.selectedFieldId;

            ctx.fillStyle = color;
            ctx.fillRect(field.x, field.y, field.w, field.h);

            // Use solid border if selected, dashed/thin if not
            if (isSelected) {
                ctx.strokeStyle = '#ef4444'; // Red border
                ctx.lineWidth = 3;
                ctx.strokeRect(field.x, field.y, field.w, field.h);
            } else {
                ctx.strokeStyle = '#4f46e5'; // Indigo border
                ctx.lineWidth = 2;
                ctx.strokeRect(field.x, field.y, field.w, field.h);
            }

            ctx.fillStyle = '#1e293b';
            ctx.font = 'bold 12px sans-serif';
            ctx.fillText(field.label || field.field_key || 'New Field', field.x + 6, field.y + 18);

            // Resize handle
            ctx.fillStyle = isSelected ? '#ef4444' : '#4f46e5';
            ctx.fillRect(field.x + field.w - handleSize, field.y + field.h - handleSize, handleSize, handleSize);
        });
    }

    function getPos(e) {
        const rect = overlay.getBoundingClientRect();
        const sx = overlay.width / rect.width;
        const sy = overlay.height / rect.height;
        return { x: (e.clientX - rect.left) * sx, y: (e.clientY - rect.top) * sy };
    }

    function hitTest(pos) {
        const rect = overlay.getBoundingClientRect();
        const handleSize = 8 * (overlay.width / (rect.width || overlay.width));
        const fields = alpineComponent.fields;

        for (let i = fields.length - 1; i >= 0; i--) {
            const f = fields[i];
            const fieldPage = f.page_number || 1;
            if (fieldPage !== currentPage) continue;
            
            // Check resize handle first
            if (pos.x >= f.x + f.w - handleSize && pos.x <= f.x + f.w && pos.y >= f.y + f.h - handleSize && pos.y <= f.y + f.h)
                return { index: i, action: 'resize' };
            // Check bounding box
            if (pos.x >= f.x && pos.x <= f.x + f.w && pos.y >= f.y && pos.y <= f.y + f.h)
                return { index: i, action: 'drag' };
        }
        return null;
    }

    overlay.addEventListener('mousedown', e => {
        const pos = getPos(e);
        const hit = hitTest(pos);
        const fields = alpineComponent.fields;
        if (hit) {
            dragIndex = hit.index;
            const clickedField = fields[dragIndex];
            alpineComponent.selectedFieldId = clickedField.id;

            if (hit.action === 'resize') {
                isResizing = true;
            } else {
                isDragging = true;
                dragOffsetX = pos.x - clickedField.x;
                dragOffsetY = pos.y - clickedField.y;
            }
            drawFields();
        } else {
            // Clicked outside, deselect or add new
            alpineComponent.selectedFieldId = null;

            const w = 150, h = 30;
            const newField = { id: nextId++, x: pos.x - w / 2, y: pos.y - h / 2, w, h, field_key: '', label: '', font_size: 12, text_alignment: 'left', page_number: currentPage };
            
            // Push directly to Alpine
            alpineComponent.fields.push(newField);
            
            dragIndex = alpineComponent.fields.length - 1;
            isDragging = true;
            dragOffsetX = w / 2;
            dragOffsetY = h / 2;
            alpineComponent.selectedFieldId = newField.id;
            
            drawFields();
        }
    });

    const moveHandler = e => {
        if (!isDragging && !isResizing || dragIndex < 0) return;
        const pos = getPos(e);
        const f = alpineComponent.fields[dragIndex];
        if (!f) return;
        if (isDragging) {
            f.x = Math.max(0, Math.min(pos.x - dragOffsetX, pageWidth - f.w));
            f.y = Math.max(0, Math.min(pos.y - dragOffsetY, pageHeight - f.h));
        } else {
            f.w = Math.max(30, pos.x - f.x);
            f.h = Math.max(16, pos.y - f.y);
        }
        drawFields();
    };

    const upHandler = () => { isDragging = false; isResizing = false; dragIndex = -1; };

    overlay.addEventListener('mousemove', moveHandler);
    overlay.addEventListener('mouseup', upHandler);
    overlay.addEventListener('mouseleave', upHandler);

    return {
        drawFields,
        setPage: pageNum => { loadPage(pageNum); }
    };
}

function initPdfViewer(containerId, pdfUrl) {
    const container = document.getElementById(containerId);
    if (!container) return;
    if (!pdfUrl || pdfUrl.trim() === '' || pdfUrl === '/storage/' || pdfUrl === '/storage') return;

    container.innerHTML = '<div class="text-center py-8 text-gray-500 flex flex-col items-center justify-center gap-2">' +
        '<svg class="animate-spin h-8 w-8 text-stormy-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">' +
        '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
        '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>' +
        '</svg><span>Loading document...</span></div>';

    let docInit;
    if (pdfUrl.startsWith('JVBERi')) {
        const binaryString = atob(pdfUrl);
        const len = binaryString.length;
        const bytes = new Uint8Array(len);
        for (let i = 0; i < len; i++) {
            bytes[i] = binaryString.charCodeAt(i);
        }
        docInit = { data: bytes };
    } else {
        docInit = { url: pdfUrl };
    }

    pdfjsLib.getDocument(docInit).promise.then(pdf => {
        container.innerHTML = '';
        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
            const canvas = document.createElement('canvas');
            canvas.className = 'w-full mb-4 shadow-sm border border-gray-200/60 rounded-xl bg-white';
            container.appendChild(canvas);

            pdf.getPage(pageNum).then(page => {
                const viewport = page.getViewport({ scale: 1.5 });
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                const renderCtx = canvas.getContext('2d');
                page.render({ canvasContext: renderCtx, viewport });
            });
        }
    }).catch(err => {
        container.innerHTML = `<div class="text-rose-500 text-center py-8 font-semibold">Failed to load PDF: ${err.message}</div>`;
    });
}

window.initPdfBuilder = initPdfBuilder;
window.initPdfViewer = initPdfViewer;
