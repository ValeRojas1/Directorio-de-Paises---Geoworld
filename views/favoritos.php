<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?></title>
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
                <a href="<?= esc(pageUrl('index.php')) ?>" class="nav-link">Inicio</a>
                <a href="<?= esc(pageUrl('index.php#explorar')) ?>" class="nav-link">Explorar</a>
                <a href="<?= esc(pageUrl('comparar.php')) ?>" class="nav-link">Comparar</a>
                <a href="<?= esc(pageUrl('favoritos.php')) ?>" class="nav-link active">Favoritos</a>
            </div>
        </div>
    </nav>

    <section class="page-hero">
        <div class="container">
            <h1>Mis favoritos</h1>
            <p>Países guardados en tu cuenta GeoWorld</p>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <div id="favoritos-content">
                <?php if (empty($_SESSION['usuario'])): ?>
                    <div class="empty-state">
                        <p>Debes iniciar sesión para ver tus favoritos.</p>
                        <a href="<?= esc(pageUrl('index.php')) ?>" class="btn btn-primary" style="margin-top:16px;">Ir al inicio</a>
                    </div>
                <?php else: ?>
                    <div class="search-loading">Cargando favoritos...</div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script>window.GEOWORLD = { usuario: <?= json_encode($_SESSION['usuario'] ?? null, JSON_UNESCAPED_UNICODE) ?> };</script>
    <script src="<?= esc(assetUrl('js/main.js')) ?>"></script>
    <script src="<?= esc(assetUrl('js/favoritos.js')) ?>"></script>
</body>
</html>
