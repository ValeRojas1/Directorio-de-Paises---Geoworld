/**
 * Vista de favoritos del usuario.
 */
(function () {
    'use strict';

    const container = document.getElementById('favoritos-content');
    const cfg = window.GEOWORLD || window.GeoWorld || {};

    if (!cfg.usuario || !container) return;

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async function cargarFavoritos() {
        try {
            const favoritos = await window.GeoWorld.apiRequest(window.GeoWorld.ENDPOINTS.favoritosListar);

            if (!favoritos.length) {
                container.innerHTML = '<div class="empty-state"><p>No tienes favoritos aún.</p><a href="/directorio_paises/index.php" class="btn btn-primary" style="margin-top:16px;">Explorar países</a></div>';
                return;
            }

            container.innerHTML = '<div class="countries-scroll">' + favoritos.map((fav) => {
                const cca2 = fav.pais_cca2.toLowerCase();
                const detalle = '/directorio_paises/detalle.php?cca2=' + encodeURIComponent(fav.pais_cca2);
                return (
                    '<article class="country-card">' +
                    '<img src="https://flagcdn.com/w320/' + escapeHtml(cca2) + '.png" alt="" class="country-flag-img">' +
                    '<div class="card-body">' +
                    '<h3>' + escapeHtml(fav.pais_nombre) + '</h3>' +
                    '<div class="card-actions">' +
                    '<a href="' + escapeHtml(detalle) + '" class="btn btn-primary">Ver detalles</a>' +
                    '<button type="button" class="btn btn-secondary btn-remove-fav" data-cca2="' + escapeHtml(fav.pais_cca2) + '">Eliminar</button>' +
                    '</div></div></article>'
                );
            }).join('') + '</div>';

            container.querySelectorAll('.btn-remove-fav').forEach((btn) => {
                btn.addEventListener('click', async () => {
                    try {
                        btn.disabled = true;
                        await window.GeoWorld.quitarFavorito(btn.getAttribute('data-cca2'));
                        cargarFavoritos();
                    } catch (error) {
                        alert(error.message);
                        btn.disabled = false;
                    }
                });
            });
        } catch (error) {
            container.innerHTML = '<div class="alert alert-error">' + escapeHtml(error.message) + '</div>';
        }
    }

    cargarFavoritos();
})();
