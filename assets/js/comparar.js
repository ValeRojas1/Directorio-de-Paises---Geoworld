/**
 * Lógica del comparador de países.
 */
(function () {
    'use strict';

    const input = document.getElementById('compare-codes');
    const btnComparar = document.getElementById('btn-comparar');
    const btnGuardar = document.getElementById('btn-guardar-comparacion');
    const msg = document.getElementById('compare-message');
    const wrap = document.getElementById('compare-result');
    const thead = document.getElementById('compare-thead');
    const tbody = document.getElementById('compare-tbody');

    let ultimosPaises = [];

    function parseCodes(value) {
        return value.split(',').map((c) => c.trim().toUpperCase()).filter(Boolean);
    }

    function formatNumber(n) {
        return new Intl.NumberFormat('es-PE').format(n || 0);
    }

    function showMessage(text, type) {
        if (!msg) return;
        msg.textContent = text;
        msg.className = 'form-message is-' + type;
    }

    function renderTable(paises) {
        ultimosPaises = paises;
        if (!wrap || !thead || !tbody) return;

        const rows = [
            { label: 'País', key: 'nombre_comun' },
            { label: 'Capital', key: 'capital' },
            { label: 'Región', key: 'region' },
            { label: 'Población', key: 'poblacion', fmt: formatNumber },
            { label: 'Área (km²)', key: 'area_km2', fmt: (v) => (v ? formatNumber(v) : '—') },
            { label: 'Idiomas', key: 'idiomas', fmt: (v) => (v || []).join(', ') || '—' },
            { label: 'Monedas', key: 'monedas', fmt: (v) => (v || []).map((m) => m.nombre).join(', ') || '—' },
        ];

        thead.innerHTML = '<tr><th>Campo</th>' + paises.map((p) =>
            `<th><img src="${p.bandera_png}" alt="" width="24" style="vertical-align:middle;margin-right:6px;">${p.nombre_comun}</th>`
        ).join('') + '</tr>';

        tbody.innerHTML = rows.map((row) =>
            '<tr><td><strong>' + row.label + '</strong></td>' + paises.map((p) => {
                let val = p[row.key];
                if (row.fmt) val = row.fmt(val);
                return '<td>' + (val || '—') + '</td>';
            }).join('') + '</tr>'
        ).join('');

        wrap.style.display = 'block';
    }

    async function comparar() {
        const codes = parseCodes(input ? input.value : '');
        if (codes.length < 2) {
            showMessage('Ingresa al menos 2 códigos cca2', 'error');
            return;
        }

        try {
            showMessage('Comparando países...', 'success');
            btnComparar.disabled = true;
            const data = await window.GeoWorld.compararPaises(codes);
            renderTable(data.paises || []);
            showMessage('Comparación lista (' + data.total + ' países)', 'success');
        } catch (error) {
            showMessage(error.message, 'error');
        } finally {
            btnComparar.disabled = false;
        }
    }

    async function guardar() {
        const codes = ultimosPaises.map((p) => p.cca2);
        if (codes.length < 2) {
            showMessage('Primero realiza una comparación', 'error');
            return;
        }

        try {
            await window.GeoWorld.guardarComparacion('Comparación ' + codes.join(' vs '), codes, [
                'poblacion', 'area_km2', 'idiomas', 'monedas', 'capital', 'region',
            ]);
            showMessage('Comparación guardada', 'success');
        } catch (error) {
            showMessage(error.message, 'error');
        }
    }

    if (btnComparar) btnComparar.addEventListener('click', comparar);
    if (btnGuardar) btnGuardar.addEventListener('click', guardar);

    if (window.PAISES_INICIALES && window.PAISES_INICIALES.length >= 2 && input) {
        input.value = window.PAISES_INICIALES.join(',');
        comparar();
    }
})();
