<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?></title>    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= esc(assetUrl('css/styles.css')) ?>">
</head>
<body>

    <nav class="navbar">
        <div class="container">
            <a href="<?= esc(pageUrl('index.php')) ?>" class="logo">🌍 GeoWorld</a>
            <div class="nav-links">
                <a href="<?= esc(pageUrl('index.php')) ?>" class="nav-link">Inicio</a>
                <a href="<?= esc(pageUrl('index.php#explorar')) ?>" class="nav-link active">Explorar</a>
                <a href="<?= esc(pageUrl('comparar.php')) ?>" class="nav-link">Comparar</a>
                <a href="<?= esc(pageUrl('favoritos.php')) ?>" class="nav-link">Favoritos</a>
            </div>
            <div class="nav-actions">
                <?php if (!empty($_SESSION['usuario'])): ?>
                    <a href="<?= esc(pageUrl('perfil.php')) ?>" class="btn btn-outline">Mi Perfil</a>
                <?php else: ?>
                    <a href="<?= esc(pageUrl('index.php')) ?>" class="btn btn-primary">Iniciar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section class="page-hero">
        <div class="container">
            <?php if ($error): ?>
                <h1>País no encontrado</h1>
                <p><?= esc($error) ?></p>
            <?php else: ?>
                <h1><?= esc($pais['nombre_comun']) ?></h1>
                <p><?= esc($pais['nombre_oficial']) ?></p>
            <?php endif; ?>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <?php if ($error): ?>
                <div class="alert alert-error"><?= esc($error) ?></div>
                <a href="<?= esc(pageUrl('index.php')) ?>" class="btn btn-primary">Volver al inicio</a>
            <?php else: ?>
                <div class="detail-grid">
                    <div>
                        <img src="<?= esc($pais['bandera_png']) ?>" alt="Bandera de <?= esc($pais['nombre_comun']) ?>" class="detail-flag">
                        <div style="margin-top:16px;display:flex;gap:8px;flex-wrap:wrap;">
                            <button type="button" class="btn btn-secondary btn-add-fav"
                                data-cca2="<?= esc($pais['cca2']) ?>"
                                data-nombre="<?= esc($pais['nombre_comun']) ?>"
                                data-bandera="<?= esc($pais['bandera_svg']) ?>">❤️ Agregar a favoritos</button>
                            <a href="<?= esc(pageUrl('comparar.php?paises=' . urlencode($pais['cca2']))) ?>" class="btn btn-outline">Comparar</a>
                        </div>
                    </div>
                    <div class="detail-info">
                        <div class="info-row"><span class="info-label">Código</span><span><?= esc($pais['cca2']) ?> / <?= esc($pais['cca3']) ?></span></div>
                        <div class="info-row"><span class="info-label">Capital</span><span><?= esc($pais['capital'] ?? '—') ?></span></div>
                        <div class="info-row"><span class="info-label">Región</span><span><?= esc($pais['region']) ?> · <?= esc($pais['subregion']) ?></span></div>
                        <div class="info-row"><span class="info-label">Población</span><span><?= esc(formatNumber((int) $pais['poblacion'])) ?></span></div>
                        <div class="info-row"><span class="info-label">Área</span><span><?= $pais['area_km2'] ? esc(formatNumber((int) $pais['area_km2'])) . ' km²' : '—' ?></span></div>
                        <div class="info-row"><span class="info-label">Idiomas</span><span><?= esc(implode(', ', $pais['idiomas'] ?: ['—'])) ?></span></div>
                        <div class="info-row"><span class="info-label">Monedas</span><span>
                            <?php if (empty($pais['monedas'])): ?>
                                —
                            <?php else: ?>
                                <?= esc(implode(', ', array_map(static function ($m) {
                                    return ($m['nombre'] ?? '') . ' (' . ($m['codigo'] ?? '') . ')';
                                }, $pais['monedas']))) ?>
                            <?php endif; ?>
                        </span></div>
                        <div class="info-row"><span class="info-label">Fronteras</span><span><?= esc(implode(', ', $pais['fronteras'] ?: ['Ninguna registrada'])) ?></span></div>
                        <?php if ($pais['lat'] !== null && $pais['lng'] !== null): ?>
                        <div class="info-row"><span class="info-label">Coordenadas</span><span><?= esc($pais['lat']) ?>, <?= esc($pais['lng']) ?></span></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="<?= esc(assetUrl('js/main.js')) ?>"></script>
</body>
</html>
