import * as pdfjsLib from 'pdfjs-dist';

pdfjsLib.GlobalWorkerOptions.workerSrc = new URL(
    'pdfjs-dist/build/pdf.worker.min.mjs',
    import.meta.url,
).toString();

function initPdfBuilder(canvasId, overlayId, pdfUrl, existingFields) {
    const canvas = document.getElementById(canvasId);
    const overlay = document.getElementById(overlayId);
    if (!canvas || !overlay) return null;

    overlay.style.display = 'block';

    const ctx = overlay.getContext('2d');
    let fields = existingFields || [];
    let pageWidth = 0;
    let pageHeight = 0;
    let isDragging = false;
    let dragIndex = -1;
    let dragOffsetX = 0;
    let dragOffsetY = 0;
    let isResizing = false;
    let nextId = fields.length > 0 ? Math.max(...fields.map(f => f.id || 0)) + 1 : 1;

    const fieldColors = [
        'rgba(99, 102, 241, 0.3)', 'rgba(239, 68, 68, 0.3)',
        'rgba(34, 197, 94, 0.3)', 'rgba(234, 179, 8, 0.3)', 'rgba(168, 85, 247, 0.3)',
    ];

    pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
        pdf.getPage(1).then(page => {
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
    });

    function drawFields() {
        ctx.clearRect(0, 0, overlay.width, overlay.height);
        fields.forEach((field, i) => {
            const color = fieldColors[i % fieldColors.length];
            ctx.fillStyle = color;
            ctx.strokeStyle = '#4f46e5';
            ctx.lineWidth = 2;
            ctx.fillRect(field.x, field.y, field.w, field.h);
            ctx.strokeRect(field.x, field.y, field.w, field.h);

            ctx.fillStyle = '#1e293b';
            ctx.font = '12px sans-serif';
            ctx.fillText(field.label || field.field_key || 'New Field', field.x + 4, field.y + 16);

            const s = 8;
            ctx.fillStyle = '#4f46e5';
            ctx.fillRect(field.x + field.w - s, field.y + field.h - s, s, s);
        });
    }

    function getPos(e) {
        const rect = overlay.getBoundingClientRect();
        const sx = overlay.width / rect.width;
        const sy = overlay.height / rect.height;
        return { x: (e.clientX - rect.left) * sx, y: (e.clientY - rect.top) * sy };
    }

    function hitTest(pos) {
        for (let i = fields.length - 1; i >= 0; i--) {
            const f = fields[i];
            const s = 8 * (overlay.width / overlay.getBoundingClientRect().width);
            if (pos.x >= f.x + f.w - s && pos.x <= f.x + f.w && pos.y >= f.y + f.h - s && pos.y <= f.y + f.h)
                return { index: i, action: 'resize' };
            if (pos.x >= f.x && pos.x <= f.x + f.w && pos.y >= f.y && pos.y <= f.y + f.h)
                return { index: i, action: 'drag' };
        }
        return null;
    }

    overlay.addEventListener('mousedown', e => {
        const pos = getPos(e);
        const hit = hitTest(pos);
        if (hit) {
            dragIndex = hit.index;
            if (hit.action === 'resize') {
                isResizing = true;
            } else {
                isDragging = true;
                dragOffsetX = pos.x - fields[hit.index].x;
                dragOffsetY = pos.y - fields[hit.index].y;
            }
        } else {
            const w = 150, h = 30;
            const newField = { id: nextId++, x: pos.x - w / 2, y: pos.y - h / 2, w, h, field_key: '', label: '', font_size: 12, text_alignment: 'left' };
            fields.push(newField);
            dragIndex = fields.length - 1;
            isDragging = true;
            dragOffsetX = w / 2;
            dragOffsetY = h / 2;
            drawFields();
            const alpineEl = document.querySelector('[x-data="templateBuilder()"]');
            if (alpineEl?.__x) alpineEl.__x.$data.fields.push({ ...newField });
        }
    });

    const moveHandler = e => {
        if (!isDragging && !isResizing || dragIndex < 0) return;
        const pos = getPos(e);
        if (isDragging) {
            fields[dragIndex].x = Math.max(0, Math.min(pos.x - dragOffsetX, pageWidth - fields[dragIndex].w));
            fields[dragIndex].y = Math.max(0, Math.min(pos.y - dragOffsetY, pageHeight - fields[dragIndex].h));
        } else {
            const f = fields[dragIndex];
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
        getFields: () => fields,
        setFields: newFields => { fields = newFields; drawFields(); },
        deleteField: id => { fields = fields.filter(f => f.id !== id); drawFields(); },
        updateField: (id, data) => { const idx = fields.findIndex(f => f.id === id); if (idx >= 0) { Object.assign(fields[idx], data); drawFields(); } },
    };
}

window.initPdfBuilder = initPdfBuilder;
