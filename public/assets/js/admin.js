/*
 * =============================================================================
 * SCRIPT FRONT: PUBLIC/ASSETS/JS/ADMIN.JS
 * =============================================================================
 * QUÉ HACE: COMPORTAMIENTO EN EL NAVEGADOR (UI, PETICIONES, INTEGRACIONES) PARA ESTA PARTE DEL SITIO.
 * POR QUÍ EN JS: INTERACTIVIDAD SIN RECARGA; SE CARGA DONDE LO PIDE EL LAYOUT O LA VISTA.
 * =============================================================================
 */

/*
 * =============================================================================
 * SCRIPT FRONT: PUBLIC/ASSETS/JS/ADMIN.JS
 * =============================================================================
 * QUÉ HACE: COMPORTAMIENTO EN EL NAVEGADOR (UI, PETICIONES, INTEGRACIONES) PARA ESTA PARTE DEL SITIO.
 * POR QUÍ EN JS: INTERACTIVIDAD SIN RECARGA; SE CARGA DONDE LO PIDE EL LAYOUT O LA VISTA.
 * =============================================================================
 */

/**
 * JAVASCRIPT DEL PANEL DE ADMINISTRACIÓN.
 * SIDEBAR MÓVIL, INTERRUPTORES AJAX, MARCAR LEÍDO, CONFIRMACIÓN DE BORRADO,
 * ZONA DE SUBIDA CON VISTA PREVIA, GENERACIÓN DE SLUG Y FILAS DE VARIANTES.
 */
(function () {
    'use strict';

    /* BARRA LATERAL Y OVERLAY EN VISTA MÓVIL */
    const sidebar = document.querySelector('.admin-sidebar');
    const overlay = document.querySelector('.admin-sidebar-overlay');
    const toggleBtn = document.querySelector('.admin-topbar-toggle');

    /* MUESTRA LA SIDEBAR Y BLOQUEA EL SCROLL DEL BODY */
    function openSidebar() {
        sidebar?.classList.add('show');
        overlay?.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    /* OCULTA LA SIDEBAR Y RESTAURA EL SCROLL */
    function closeSidebar() {
        sidebar?.classList.remove('show');
        overlay?.classList.remove('show');
        document.body.style.overflow = '';
    }

    toggleBtn?.addEventListener('click', () => {
        sidebar?.classList.contains('show') ? closeSidebar() : openSidebar();
    });

    overlay?.addEventListener('click', closeSidebar);

    /* DEVUELVE PARES CLAVE/VALOR CSRF PARA FORMDATA */
    function getCsrf() {
        const n = document.querySelector('meta[name="csrf-token-name"]');
        const h = document.querySelector('meta[name="csrf-token-hash"]');
        return n && h ? { [n.content]: h.content } : {};
    }

    /* INTERRUPTORES (ACTIVO/DESTACADO): POST Y REVIERTE SI EL SERVIDOR FALLA */
    document.addEventListener('change', function (e) {
        const toggle = e.target.closest('.admin-toggle[data-url]');
        if (!toggle) return;

        const url = toggle.dataset.url;
        const body = new FormData();
        Object.entries(getCsrf()).forEach(([k, v]) => body.append(k, v));
        body.append('value', toggle.checked ? '1' : '0');

        fetch(url, { method: 'POST', body: body, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    toggle.checked = !toggle.checked;
                }
            })
            .catch(() => {
                toggle.checked = !toggle.checked;
            });
    });

    /* BOTÓN MARCAR COMO LEÍDO EN LISTADOS DE MENSAJES */
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.mark-read-btn');
        if (!btn) return;
        e.preventDefault();
        const url = btn.dataset.url;
        const body = new FormData();
        Object.entries(getCsrf()).forEach(([k, v]) => body.append(k, v));

        fetch(url, { method: 'POST', body: body, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const ind = btn.closest('tr')?.querySelector('.read-indicator');
                    if (ind) {
                        ind.classList.remove('text-secondary');
                        ind.classList.add('text-success');
                    }
                    btn.remove();
                }
            })
            .catch(() => {});
    });

    /* CONFIRMACIÓN ANTES DE ENVIAR FORMULARIOS DE ELIMINACIÓN */
    document.addEventListener('submit', function (e) {
        const form = e.target.closest('.delete-form');
        if (!form) return;
        if (!confirm('¿Estás seguro de que quieres eliminar este elemento?')) {
            e.preventDefault();
        }
    });

    /* ZONA ARRASTRAR/SOLTAR Y VISTA PREVIA DE IMÁGENES NUEVAS */
    const uploadZone = document.querySelector('.upload-zone');
    const fileInput = document.querySelector('.upload-zone-input');
    const previewGrid = document.querySelector('.image-preview-grid');

    if (uploadZone && fileInput) {
        uploadZone.addEventListener('click', () => fileInput.click());

        ['dragenter', 'dragover'].forEach(ev => {
            uploadZone.addEventListener(ev, (e) => { e.preventDefault(); uploadZone.classList.add('dragover'); });
        });
        ['dragleave', 'drop'].forEach(ev => {
            uploadZone.addEventListener(ev, (e) => { e.preventDefault(); uploadZone.classList.remove('dragover'); });
        });

        uploadZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            if (dt.files.length) {
                fileInput.files = dt.files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });

        fileInput.addEventListener('change', () => {
            if (!previewGrid) return;
            const existingPreviews = previewGrid.querySelectorAll('.preview-new');
            existingPreviews.forEach(p => p.remove());

            Array.from(fileInput.files).forEach(file => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'image-preview-item preview-new';
                    div.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                    previewGrid.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    /* SLUG AUTOMÁTICO DESDE NOMBRE O TÍTULO SI EL SLUG ESTÁ VACÍO */
    const nameInput = document.getElementById('name') || document.getElementById('title');
    const slugInput = document.getElementById('slug');
    if (nameInput && slugInput && !slugInput.value) {
        nameInput.addEventListener('input', () => {
            slugInput.value = nameInput.value
                .toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/(^-|-$)/g, '');
        });
    }

    /* AÑADIR FILA DE VARIANTE DE PRODUCTO Y ELIMINAR FILA */
    const addVariantBtn = document.getElementById('add-variant-btn');
    const variantTable = document.getElementById('variant-table-body');
    if (addVariantBtn && variantTable) {
        addVariantBtn.addEventListener('click', () => {
            const idx = variantTable.children.length;
            const row = document.createElement('tr');
            row.innerHTML =
                '<td><input type="text" class="form-control form-control-sm" name="variants[' + idx + '][variant_name]" placeholder="Ej: Color"></td>' +
                '<td><input type="text" class="form-control form-control-sm" name="variants[' + idx + '][variant_value]" placeholder="Ej: Rojo"></td>' +
                '<td><input type="number" class="form-control form-control-sm" name="variants[' + idx + '][price_modifier]" step="0.01" value="0"></td>' +
                '<td><input type="number" class="form-control form-control-sm" name="variants[' + idx + '][stock]" value="0" min="0"></td>' +
                '<td><input type="text" class="form-control form-control-sm" name="variants[' + idx + '][sku]" placeholder="SKU"></td>' +
                '<td><button type="button" class="btn btn-sm btn-outline-danger remove-variant-btn"><i class="bi bi-trash"></i></button></td>';
            variantTable.appendChild(row);
        });

        variantTable.addEventListener('click', (e) => {
            if (e.target.closest('.remove-variant-btn')) {
                e.target.closest('tr')?.remove();
            }
        });
    }
})();
