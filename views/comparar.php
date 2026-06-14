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
                <a href="<?= esc(pageUrl('comparar.php')) ?>" class="nav-link active">Comparar</a>
                <a href="<?= esc(pageUrl('favoritos.php')) ?>" class="nav-link">Favoritos</a>
            </div>
            <div class="nav-actions">
                <a href="<?= esc(pageUrl('index.php')) ?>" class="btn btn-outline">Buscar países</a>
            </div>
        </div>
    </nav>

    <section class="page-hero">
        <div class="container">
            <h1>Comparador de países</h1>
            <p>Selecciona de 2 a 6 países por código ISO (cca2) separados por comas.</p>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <div class="form-card" style="max-width:100%;margin-bottom:32px;">
                <div class="form-group">
                    <label for="compare-codes">Códigos cca2 (ej: PE,CO,CL)</label>
                    <input type="text" id="compare-codes" value="<?= esc(implode(',', $paisesIniciales ?? [])) ?>" placeholder="PE,CO,CL">
                </div>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <button type="button" class="btn btn-primary" id="btn-comparar">Comparar ahora</button>
                    <?php if (!empty($_SESSION['usuario'])): ?>
                    <button type="button" class="btn btn-secondary" id="btn-guardar-comparacion">Guardar comparación</button>
                    <?php endif; ?>
                </div>
                <div class="form-message" id="compare-message"></div>
            </div>

            <div class="comparison-table-wrap" id="compare-result" style="display:none;">
                <table class="comparison-table">
                    <thead id="compare-thead"></thead>
                    <tbody id="compare-tbody"></tbody>
                </table>
            </div>
        </div>
    </section>

    <script>
        window.GEOWORLD = { usuario: <?= json_encode($_SESSION['usuario'] ?? null, JSON_UNESCAPED_UNICODE) ?> };
        window.PAISES_INICIALES = <?= json_encode(array_values($paisesIniciales ?? []), JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <script src="<?= esc(assetUrl('js/main.js')) ?>"></script>
    <script src="<?= esc(assetUrl('js/comparar.js')) ?>"></script>
</body>
</html>
