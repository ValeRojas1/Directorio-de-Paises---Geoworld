<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'GeoWorld — Directorio de Países') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= esc(assetUrl('css/styles.css')) ?>">
</head>
<body>

    <nav class="navbar">
        <div class="container">
            <a href="<?= esc(pageUrl('index.php')) ?>" class="logo">🌍 GeoWorld</a>
            <div class="nav-links">
                <a href="<?= esc(pageUrl('index.php')) ?>" class="nav-link <?= ($activeNav ?? '') === 'inicio' ? 'active' : '' ?>">Inicio</a>
                <a href="<?= esc(pageUrl('index.php#explorar')) ?>" class="nav-link">Explorar</a>
                <a href="<?= esc(pageUrl('comparar.php')) ?>" class="nav-link <?= ($activeNav ?? '') === 'comparar' ? 'active' : '' ?>">Comparar</a>
                <a href="<?= esc(pageUrl('favoritos.php')) ?>" class="nav-link <?= ($activeNav ?? '') === 'favoritos' ? 'active' : '' ?>">Favoritos</a>
            </div>
            <div class="nav-actions" id="nav-auth">
                <?php if (!empty($_SESSION['usuario'])): ?>
                    <span class="nav-user">Hola, <?= esc($_SESSION['usuario']['nombre']) ?></span>
                    <a href="<?= esc(pageUrl('perfil.php')) ?>" class="btn btn-outline">Mi Perfil</a>
                    <button type="button" class="btn btn-secondary" id="btn-logout">Cerrar Sesión</button>
                <?php else: ?>
                    <button type="button" class="btn btn-outline" id="btn-open-login">Iniciar Sesión</button>
                    <button type="button" class="btn btn-primary" id="btn-open-register">Registrarse</button>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-content">
                    <div class="hero-badge">🌐 250+ países disponibles</div>
                    <h1>Descubre y compara
países del mundo</h1>
                    <h3>Explora información detallada de más de 250 países: idiomas, monedas, poblaciones y mucho más. Compara entre ellos en segundos.</h3>

                    <div class="search-wrapper">
                        <form class="search-bar" id="hero-search-form" autocomplete="off">
                            <span class="search-icon">🔍</span>
                            <input type="text" id="hero-search-input" name="q" placeholder="Buscar un país por nombre, capital o idioma...">
                            <button type="submit" class="btn btn-primary" id="hero-search-btn">Buscar</button>
                        </form>
                        <div class="search-results" id="search-results" aria-live="polite"></div>
                    </div>

                    <div class="hero-tags">
                        <button type="button" class="tag-pill" data-search-tipo="region" data-search-value="americas">🌍 Por región</button>
                        <button type="button" class="tag-pill" data-search-tipo="lang" data-search-value="spanish">🗣️ Por idioma</button>
                        <button type="button" class="tag-pill" data-search-tipo="currency" data-search-value="usd">💰 Por moneda</button>
                    </div>
                </div>

                <div class="hero-visual">
                    <div class="hero-cards-grid" id="hero-mini-cards">
                        <div class="mini-card">
                            <img src="https://flagcdn.com/w80/pe.png" alt="Perú" width="40">
                            <strong>Perú</strong>
                            <small>Pob: 33.2M</small>
                        </div>
                        <div class="mini-card">
                            <img src="https://flagcdn.com/w80/jp.png" alt="Japón" width="40">
                            <strong>Japón</strong>
                            <small>Pob: 125.8M</small>
                        </div>
                        <div class="mini-card">
                            <img src="https://flagcdn.com/w80/de.png" alt="Alemania" width="40">
                            <strong>Alemania</strong>
                            <small>Pob: 83.2M</small>
                        </div>
                        <div class="mini-card">
                            <img src="https://flagcdn.com/w80/br.png" alt="Brasil" width="40">
                            <strong>Brasil</strong>
                            <small>Pob: 214.3M</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($dbWarning ?? '')): ?>
        <div class="container" style="padding-top:16px;">
            <div class="alert alert-info"><?= esc($dbWarning) ?></div>
        </div>
    <?php endif; ?>

    <div class="stats-bar">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <h2><?= esc($stats['paises'] ?? '250+') ?></h2>
                    <small>Países registrados</small>
                </div>
                <div class="stat-item">
                    <h2><?= esc($stats['regiones'] ?? '5') ?></h2>
                    <small>Regiones del mundo</small>
                </div>
                <div class="stat-item">
                    <h2><?= esc($stats['idiomas'] ?? '100+') ?></h2>
                    <small>Idiomas representados</small>
                </div>
                <div class="stat-item">
                    <h2 id="stat-comparaciones"><?= esc($stats['comparaciones'] ?? '0') ?></h2>
                    <small>Comparaciones realizadas</small>
                </div>
            </div>
        </div>
    </div>

    <section class="featured" id="explorar">
        <div class="container">
            <div class="section-header">
                <h2 id="featured-title">Países destacados esta semana</h2>
                <p id="featured-subtitle">Descubridos por nuestra comunidad educativa</p>
            </div>

            <div class="countries-scroll" id="countries-featured">
                <?php foreach ($destacados as $pais): ?>
                <article class="country-card" data-cca2="<?= esc($pais['cca2']) ?>">
                    <img src="<?= esc($pais['bandera_png']) ?>" alt="Bandera de <?= esc($pais['nombre_comun']) ?>" class="country-flag-img">
                    <div class="card-body">
                        <h3><?= esc($pais['nombre_comun']) ?></h3>
                        <div class="card-meta">
                            <div class="meta-item">🏛️ <?= esc($pais['capital']) ?></div>
                            <div class="badge-pill"><?= esc($pais['subregion'] ?? '') ?></div>
                            <div class="meta-item">👥 <?= esc(formatNumber((int) $pais['poblacion'])) ?></div>
                        </div>
                        <div class="card-actions">
                            <a href="<?= esc(pageUrl('detalle.php?cca2=' . $pais['cca2'])) ?>" class="btn btn-primary">Ver detalles</a>
                            <button type="button" class="btn btn-secondary btn-fav btn-add-fav"
                                data-cca2="<?= esc($pais['cca2']) ?>"
                                data-nombre="<?= esc($pais['nombre_comun']) ?>"
                                data-bandera="<?= esc($pais['bandera_svg']) ?>">❤️ Favorito</button>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="comparator-cta">
        <div class="container">
            <div class="comparator-grid">
                <div class="comparator-info">
                    <div class="hero-badge">⚡ Función estrella</div>
                    <h2>Compara países en segundos</h2>
                    <p>Selecciona hasta 6 países y compara sus datos clave lado a lado: población, área, idiomas, monedas y fronteras. Guarda tus comparaciones favoritas.</p>
                    <ul class="check-list">
                        <li>✅ Hasta 6 países simultáneos</li>
                        <li>✅ Tabla comparativa exportable</li>
                        <li>✅ Guarda comparaciones en tu perfil</li>
                        <li>✅ Compartir con enlace único</li>
                    </ul>
                    <a href="<?= esc(pageUrl('comparar.php')) ?>" class="btn btn-primary" style="padding: 14px 28px;">Ir al Comparador →</a>
                </div>

                <div class="comparator-mockup">
                    <table class="mockup-table">
                        <thead>
                            <tr>
                                <th>País</th>
                                <th>Población</th>
                                <th>Área</th>
                                <th>Idioma</th>
                                <th>Moneda</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="country-name-cell"><img src="https://flagcdn.com/w20/pe.png" alt=""> Perú</td>
                                <td>33.2M</td>
                                <td>1.28M km²</td>
                                <td>Español</td>
                                <td>Sol</td>
                            </tr>
                            <tr>
                                <td class="country-name-cell"><img src="https://flagcdn.com/w20/co.png" alt=""> Colombia</td>
                                <td>50.9M</td>
                                <td>1.14M km²</td>
                                <td>Español</td>
                                <td>Peso</td>
                            </tr>
                            <tr>
                                <td class="country-name-cell"><img src="https://flagcdn.com/w20/cl.png" alt=""> Chile</td>
                                <td>19.1M</td>
                                <td>756K km²</td>
                                <td>Español</td>
                                <td>Peso</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="logo">🌍 GeoWorld</div>
                    <p>Tu portal educativo de geografía mundial</p>
                    <div class="api-text">Desarrollado con REST Countries API</div>
                </div>
                <div class="footer-col">
                    <h4>Explorar</h4>
                    <ul class="footer-links">
                        <li><a href="<?= esc(pageUrl('index.php#explorar')) ?>">Todos los países</a></li>
                        <li><a href="<?= esc(serviceUrl('paises/region.php?region=americas')) ?>" target="_blank" rel="noopener">Por región (API)</a></li>
                        <li><a href="<?= esc(pageUrl('index.php')) ?>">Por idioma</a></li>
                        <li><a href="<?= esc(pageUrl('index.php')) ?>">Por moneda</a></li>
                        <li><a href="<?= esc(serviceUrl('historial/top_busquedas.php')) ?>" target="_blank" rel="noopener">Más populares</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Mi Cuenta</h4>
                    <ul class="footer-links">
                        <li><a href="#" id="footer-login">Iniciar Sesión</a></li>
                        <li><a href="#" id="footer-register">Registrarse</a></li>
                        <li><a href="<?= esc(pageUrl('favoritos.php')) ?>">Mis Favoritos</a></li>
                        <li><a href="<?= esc(pageUrl('comparar.php')) ?>">Mis Comparaciones</a></li>
                        <li><a href="<?= esc(pageUrl('perfil.php')) ?>">Mi Perfil</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Info</h4>
                    <ul class="footer-links">
                        <li><a href="<?= esc(pageUrl('index.php')) ?>">Acerca de</a></li>
                        <li><a href="https://restcountries.com/" target="_blank" rel="noopener">API utilizada</a></li>
                        <li><a href="mailto:contacto@geoworld.edu">Contacto</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="footer-copy">© 2025 GeoWorld — Proyecto educativo | Universidad Continental</div>
                <div class="footer-made">Hecho con ❤️ en Perú 🇵🇪</div>
            </div>
        </div>
    </footer>

    <div class="modal-overlay" id="modal-login">
        <div class="modal">
            <div class="modal-header">
                <h3>Iniciar Sesión</h3>
                <button type="button" class="modal-close" data-close-modal>&times;</button>
            </div>
            <form id="form-login">
                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Contraseña</label>
                    <input type="password" id="login-password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%">Entrar</button>
                <div class="form-message" id="login-message"></div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="modal-register">
        <div class="modal">
            <div class="modal-header">
                <h3>Registrarse</h3>
                <button type="button" class="modal-close" data-close-modal>&times;</button>
            </div>
            <form id="form-register">
                <div class="form-group">
                    <label for="register-nombre">Nombre</label>
                    <input type="text" id="register-nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="register-email">Email</label>
                    <input type="email" id="register-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="register-password">Contraseña</label>
                    <input type="password" id="register-password" name="password" minlength="6" required>
                </div>
                <div class="form-group">
                    <label for="register-password-confirm">Confirmar contraseña</label>
                    <input type="password" id="register-password-confirm" name="password_confirm" minlength="6" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%">Crear cuenta</button>
                <div class="form-message" id="register-message"></div>
            </form>
        </div>
    </div>

    <script src="<?= esc(assetUrl('js/main.js')) ?>"></script>
    <?php if (!empty($extraScripts)): ?>
        <?php foreach ($extraScripts as $script): ?>
            <script src="<?= esc($script) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
