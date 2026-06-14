/**
 * GeoWorld — Cliente JavaScript SOA
 * Sin dependencias externas.
 */

const getBasePath = () => {
    const path = window.location.pathname;
    const parts = path.split('/');
    if (parts[parts.length - 1].includes('.')) {
        parts.pop();
    }
    return parts.join('/').replace(/\/$/, '');
};

const APP_PATH = getBasePath();
const BASE_URL = `${APP_PATH}/services`;

const ENDPOINTS = {
    buscar: `${BASE_URL}/paises/buscar.php`,
    detalle: `${BASE_URL}/paises/detalle.php`,
    region: `${BASE_URL}/paises/region.php`,
    login: `${BASE_URL}/auth/login.php`,
    register: `${BASE_URL}/auth/register.php`,
    logout: `${BASE_URL}/auth/logout.php`,
    favoritosAgregar: `${BASE_URL}/favoritos/agregar.php`,
    favoritosEliminar: `${BASE_URL}/favoritos/eliminar.php`,
    favoritosListar: `${BASE_URL}/favoritos/listar.php`,
    comparar: `${BASE_URL}/comparacion/comparar.php`,
    guardarComparacion: `${BASE_URL}/comparacion/guardar.php`,
    historialComparacion: `${BASE_URL}/comparacion/historial.php`,
    historialRegistrar: `${BASE_URL}/historial/registrar.php`,
    topBusquedas: `${BASE_URL}/historial/top_busquedas.php`,
};

const PAGE_DETALLE = `${APP_PATH}/detalle.php`;

async function apiRequest(url, options = {}) {
    const response = await fetch(url, options);
    const payload = await response.json().catch(() => ({
        success: false,
        message: 'Respuesta no válida del servidor',
    }));

    if (!response.ok || payload.success === false) {
        throw new Error(payload.message || `Error HTTP ${response.status}`);
    }

    return payload.data;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = String(text ?? '');
    return div.innerHTML;
}

function formatNumber(num) {
    return new Intl.NumberFormat('es-PE').format(num || 0);
}

function setButtonLoading(button, loading, loadingText = 'Cargando...') {
    if (!button) return;
    if (loading) {
        button.dataset.originalText = button.textContent;
        button.textContent = loadingText;
        button.disabled = true;
    } else {
        button.textContent = button.dataset.originalText || button.textContent;
        button.disabled = false;
    }
}

function renderCountryCards(paises, containerId = 'countries-featured') {
    const container = document.getElementById(containerId);
    if (!container) return;

    if (!paises.length) {
        container.innerHTML = '<div class="empty-state">No se encontraron países</div>';
        return;
    }

    container.innerHTML = paises.map((pais) => {
        const cca2Lower = String(pais.cca2 || '').toLowerCase();
        const bandera = pais.bandera_png || pais.bandera_svg || `https://flagcdn.com/w320/${cca2Lower}.png`;
        const detalleUrl = `${PAGE_DETALLE}?cca2=${encodeURIComponent(pais.cca2)}`;

        return `
            <article class="country-card" data-cca2="${escapeHtml(pais.cca2)}">
                <img src="${escapeHtml(bandera)}" alt="Bandera de ${escapeHtml(pais.nombre_comun)}" class="country-flag-img">
                <div class="card-body">
                    <h3>${escapeHtml(pais.nombre_comun)}</h3>
                    <div class="card-meta">
                        <div class="meta-item">🏛️ ${escapeHtml(pais.capital || '—')}</div>
                        <div class="badge-pill">${escapeHtml(pais.subregion || pais.region || '—')}</div>
                        <div class="meta-item">👥 ${formatNumber(pais.poblacion)}</div>
                    </div>
                    <div class="card-actions">
                        <a href="${escapeHtml(detalleUrl)}" class="btn btn-primary">Ver detalles</a>
                        <button type="button" class="btn btn-secondary btn-fav btn-add-fav"
                            data-cca2="${escapeHtml(pais.cca2)}"
                            data-nombre="${escapeHtml(pais.nombre_comun)}"
                            data-bandera="${escapeHtml(pais.bandera_svg || `https://flagcdn.com/${cca2Lower}.svg`)}">
                            ❤️ Favorito
                        </button>
                    </div>
                </div>
            </article>
        `;
    }).join('');

    bindFavoriteButtons();
}

function renderSearchDropdown(paises) {
    const searchResults = document.getElementById('search-results');
    if (!searchResults) return;

    if (!paises.length) {
        searchResults.innerHTML = '<div class="search-empty">No se encontraron países</div>';
        searchResults.classList.add('is-visible');
        return;
    }

    searchResults.innerHTML = paises.slice(0, 8).map((pais) => {
        const url = `${PAGE_DETALLE}?cca2=${encodeURIComponent(pais.cca2)}`;
        const bandera = pais.bandera_png || pais.bandera_svg;
        return `
            <a class="search-result-item" href="${escapeHtml(url)}">
                <img src="${escapeHtml(bandera)}" alt="">
                <div>
                    <strong>${escapeHtml(pais.nombre_comun)}</strong>
                    <div class="search-result-meta">${escapeHtml(pais.capital || 'Sin capital')} · ${escapeHtml(pais.region || '')}</div>
                </div>
            </a>
        `;
    }).join('');

    searchResults.classList.add('is-visible');
}

async function buscarPais(termino, button = null) {
    const q = String(termino || '').trim();
    if (q.length < 2) {
        throw new Error('Ingresa al menos 2 caracteres');
    }

    setButtonLoading(button, true);

    try {
        const data = await apiRequest(`${ENDPOINTS.buscar}?q=${encodeURIComponent(q)}`);
        renderSearchDropdown(data.paises || []);
        renderCountryCards(data.paises || []);

        const featuredTitle = document.getElementById('featured-title');
        const featuredSubtitle = document.getElementById('featured-subtitle');
        if (featuredTitle) featuredTitle.textContent = `Resultados para "${q}"`;
        if (featuredSubtitle) featuredSubtitle.textContent = `${data.total || 0} países encontrados`;

        return data;
    } finally {
        setButtonLoading(button, false);
    }
}

async function verDetalle(cca2) {
    const code = String(cca2 || '').trim().toUpperCase();
    if (!code) throw new Error('Código cca2 requerido');

    await apiRequest(`${ENDPOINTS.detalle}?cca2=${encodeURIComponent(code)}`);
    window.location.href = `${PAGE_DETALLE}?cca2=${encodeURIComponent(code)}`;
}

async function agregarFavorito(cca2, nombre, bandera, button = null) {
    setButtonLoading(button, true, 'Guardando...');

    try {
        await apiRequest(ENDPOINTS.favoritosAgregar, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                pais_cca2: cca2,
                pais_nombre: nombre,
                pais_bandera_url: bandera,
            }),
        });

        if (button) {
            button.textContent = '✓ Guardado';
            button.disabled = true;
        }
    } catch (error) {
        if (error.message.includes('401')) {
            openModal('modal-login');
        }
        throw error;
    } finally {
        if (button && !button.disabled) {
            setButtonLoading(button, false);
        }
    }
}

async function quitarFavorito(cca2) {
    return apiRequest(ENDPOINTS.favoritosEliminar, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pais_cca2: cca2 }),
    });
}

async function compararPaises(listaCca2) {
    const paises = Array.isArray(listaCca2)
        ? listaCca2
        : String(listaCca2 || '').split(',').map((c) => c.trim()).filter(Boolean);

    return apiRequest(ENDPOINTS.comparar, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cca2: paises }),
    });
}

async function guardarComparacion(titulo, paises, campos) {
    const paisesStr = Array.isArray(paises) ? paises.join(',') : String(paises || '');

    return apiRequest(ENDPOINTS.guardarComparacion, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            titulo,
            paises_cca2: paisesStr,
            campos_comparados: campos,
        }),
    });
}

async function login(email, password) {
    return apiRequest(ENDPOINTS.login, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password }),
    });
}

async function register(nombre, email, pass, passConfirm) {
    return apiRequest(ENDPOINTS.register, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            nombre,
            email,
            password: pass,
            password_confirm: passConfirm,
        }),
    });
}

async function logout() {
    await apiRequest(ENDPOINTS.logout, { method: 'POST' });
    window.location.reload();
}

function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('is-open');
}

function closeModals() {
    document.querySelectorAll('.modal-overlay').forEach((modal) => modal.classList.remove('is-open'));
}

function bindFavoriteButtons() {
    document.querySelectorAll('.btn-add-fav').forEach((btn) => {
        if (btn.dataset.bound === '1') return;
        btn.dataset.bound = '1';

        btn.addEventListener('click', async () => {
            try {
                await agregarFavorito(
                    btn.dataset.cca2,
                    btn.dataset.nombre,
                    btn.dataset.bandera,
                    btn
                );
            } catch (error) {
                alert(error.message);
            }
        });
    });
}

function initNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    const onScroll = function () {
        navbar.classList.toggle('is-scrolled', window.scrollY > 8);
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

function initLanding() {
    initNavbarScroll();
    const searchForm = document.getElementById('hero-search-form');
    const searchInput = document.getElementById('hero-search-input');
    const searchBtn = document.getElementById('hero-search-btn');
    const searchResults = document.getElementById('search-results');
    let debounceTimer = null;

    if (searchForm) {
        searchForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            try {
                await buscarPais(searchInput?.value, searchBtn);
                if (searchResults) searchResults.classList.remove('is-visible');
            } catch (error) {
                alert(error.message);
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(async () => {
                const value = searchInput.value.trim();
                if (value.length < 2) {
                    if (searchResults) searchResults.classList.remove('is-visible');
                    return;
                }
                try {
                    const data = await apiRequest(`${ENDPOINTS.buscar}?q=${encodeURIComponent(value)}`);
                    renderSearchDropdown(data.paises || []);
                } catch (error) {
                    if (searchResults) {
                        searchResults.innerHTML = `<div class="search-empty">${escapeHtml(error.message)}</div>`;
                        searchResults.classList.add('is-visible');
                    }
                }
            }, 350);
        });
    }

    document.addEventListener('click', (event) => {
        if (searchResults && !searchResults.contains(event.target) && event.target !== searchInput) {
            searchResults.classList.remove('is-visible');
        }
    });

    document.querySelectorAll('[data-search-tipo="region"]').forEach((pill) => {
        pill.addEventListener('click', async () => {
            const region = pill.getAttribute('data-search-value') || 'americas';
            try {
                pill.disabled = true;
                const data = await apiRequest(`${ENDPOINTS.region}?region=${encodeURIComponent(region)}`);
                renderCountryCards(data.paises || []);
                const featuredTitle = document.getElementById('featured-title');
                const featuredSubtitle = document.getElementById('featured-subtitle');
                if (featuredTitle) featuredTitle.textContent = `Países de ${region}`;
                if (featuredSubtitle) featuredSubtitle.textContent = `${data.total || 0} países encontrados`;
            } catch (error) {
                alert(error.message);
            } finally {
                pill.disabled = false;
            }
        });
    });

    const loginBtn = document.getElementById('btn-open-login');
    const registerBtn = document.getElementById('btn-open-register');
    const footerLogin = document.getElementById('footer-login');
    const footerRegister = document.getElementById('footer-register');
    const logoutBtn = document.getElementById('btn-logout');

    if (loginBtn) loginBtn.addEventListener('click', () => openModal('modal-login'));
    if (registerBtn) registerBtn.addEventListener('click', () => openModal('modal-register'));
    if (footerLogin) footerLogin.addEventListener('click', (e) => { e.preventDefault(); openModal('modal-login'); });
    if (footerRegister) footerRegister.addEventListener('click', (e) => { e.preventDefault(); openModal('modal-register'); });
    if (logoutBtn) logoutBtn.addEventListener('click', () => logout().catch((e) => alert(e.message)));

    document.querySelectorAll('[data-close-modal]').forEach((btn) => {
        btn.addEventListener('click', closeModals);
    });

    document.querySelectorAll('.modal-overlay').forEach((overlay) => {
        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) closeModals();
        });
    });

    const formLogin = document.getElementById('form-login');
    if (formLogin) {
        formLogin.addEventListener('submit', async (event) => {
            event.preventDefault();
            const msg = document.getElementById('login-message');
            const submitBtn = formLogin.querySelector('button[type="submit"]');
            setButtonLoading(submitBtn, true);
            try {
                await login(
                    document.getElementById('login-email').value,
                    document.getElementById('login-password').value
                );
                window.location.reload();
            } catch (error) {
                if (msg) {
                    msg.textContent = error.message;
                    msg.className = 'form-message is-error';
                }
            } finally {
                setButtonLoading(submitBtn, false);
            }
        });
    }

    const formRegister = document.getElementById('form-register');
    if (formRegister) {
        formRegister.addEventListener('submit', async (event) => {
            event.preventDefault();
            const msg = document.getElementById('register-message');
            const submitBtn = formRegister.querySelector('button[type="submit"]');
            setButtonLoading(submitBtn, true);
            try {
                await register(
                    document.getElementById('register-nombre').value,
                    document.getElementById('register-email').value,
                    document.getElementById('register-password').value,
                    document.getElementById('register-password-confirm').value
                );
                window.location.reload();
            } catch (error) {
                if (msg) {
                    msg.textContent = error.message;
                    msg.className = 'form-message is-error';
                }
            } finally {
                setButtonLoading(submitBtn, false);
            }
        });
    }

    bindFavoriteButtons();
}

document.addEventListener('DOMContentLoaded', initLanding);

window.GeoWorld = {
    BASE_URL,
    ENDPOINTS,
    buscarPais,
    verDetalle,
    agregarFavorito,
    quitarFavorito,
    compararPaises,
    guardarComparacion,
    login,
    register,
    logout,
    renderCountryCards,
    apiRequest,
};

window.GeoWorldApp = window.GeoWorld;
