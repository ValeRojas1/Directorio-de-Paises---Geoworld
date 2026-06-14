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
                <a href="<?= esc(pageUrl('comparar.php')) ?>" class="nav-link">Comparar</a>
                <a href="<?= esc(pageUrl('favoritos.php')) ?>" class="nav-link">Favoritos</a>
                <a href="<?= esc(pageUrl('perfil.php')) ?>" class="nav-link active">Perfil</a>
            </div>
        </div>
    </nav>

    <section class="page-hero">
        <div class="container">
            <h1>Mi perfil</h1>
            <?php if (!empty($_SESSION['usuario'])): ?>
                <p>Bienvenido, <?= esc($_SESSION['usuario']['nombre']) ?></p>
            <?php else: ?>
                <p>Inicia sesión para acceder a tu perfil</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <?php if (empty($_SESSION['usuario'])): ?>
                <div class="empty-state">
                    <p>No has iniciado sesión.</p>
                    <a href="<?= esc(pageUrl('index.php')) ?>" class="btn btn-primary" style="margin-top:16px;">Iniciar sesión</a>
                </div>
            <?php else: ?>
                <div class="detail-info" style="margin-bottom:40px;">
                    <div class="info-row"><span class="info-label">Nombre</span><span><?= esc($_SESSION['usuario']['nombre']) ?></span></div>
                    <div class="info-row"><span class="info-label">Email</span><span><?= esc($_SESSION['usuario']['email']) ?></span></div>
                    <div class="info-row"><span class="info-label">Rol</span><span><?= esc($_SESSION['usuario']['rol']) ?></span></div>
                </div>

                <h2 style="margin-bottom:24px;">Mis comparaciones guardadas</h2>

                <?php if (!empty($dbError ?? '')): ?>
                    <div class="alert alert-error"><?= esc($dbError) ?></div>
                <?php elseif (empty($comparaciones)): ?>
                    <div class="empty-state"><p>Aún no has guardado comparaciones.</p></div>
                <?php else: ?>
                    <div class="comparison-table-wrap">
                        <table class="comparison-table">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Países</th>
                                    <th>Fecha</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($comparaciones as $comp):
                                    $paises = json_decode((string) $comp['paises_cca2'], true) ?: [];
                                ?>
                                <tr>
                                    <td><?= esc($comp['titulo']) ?></td>
                                    <td><?= esc(implode(', ', $paises)) ?></td>
                                    <td><?= esc(date('d/m/Y H:i', strtotime($comp['created_at']))) ?></td>
                                    <td>
                                        <a href="<?= esc(pageUrl('comparar.php?paises=' . urlencode(implode(',', $paises)))) ?>" class="btn btn-outline" style="padding:8px 12px;font-size:13px;">Ver</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div style="margin-top:32px;display:flex;gap:12px;">
                    <a href="<?= esc(pageUrl('favoritos.php')) ?>" class="btn btn-primary">Mis favoritos</a>
                    <button type="button" class="btn btn-secondary" id="btn-logout">Cerrar sesión</button>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="<?= esc(assetUrl('js/main.js')) ?>"></script>
</body>
</html>
