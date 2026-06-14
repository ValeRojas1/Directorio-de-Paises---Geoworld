<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/app.php';

$pageTitle = 'GeoWorld — Directorio de Países';
$activeNav = 'inicio';

$stats = [
    'paises'        => '250+',
    'regiones'      => '5',
    'idiomas'       => '100+',
    'comparaciones' => '0',
];

$destacados = [
    ['cca2' => 'PE', 'nombre_comun' => 'Perú', 'capital' => 'Lima', 'subregion' => 'América del Sur', 'poblacion' => 33191428, 'bandera_png' => 'https://flagcdn.com/w320/pe.png', 'bandera_svg' => 'https://flagcdn.com/pe.svg'],
    ['cca2' => 'CO', 'nombre_comun' => 'Colombia', 'capital' => 'Bogotá', 'subregion' => 'América del Sur', 'poblacion' => 50882884, 'bandera_png' => 'https://flagcdn.com/w320/co.png', 'bandera_svg' => 'https://flagcdn.com/co.svg'],
    ['cca2' => 'JP', 'nombre_comun' => 'Japón', 'capital' => 'Tokio', 'subregion' => 'Asia', 'poblacion' => 125836021, 'bandera_png' => 'https://flagcdn.com/w320/jp.png', 'bandera_svg' => 'https://flagcdn.com/jp.svg'],
    ['cca2' => 'DE', 'nombre_comun' => 'Alemania', 'capital' => 'Berlín', 'subregion' => 'Europa', 'poblacion' => 83240525, 'bandera_png' => 'https://flagcdn.com/w320/de.png', 'bandera_svg' => 'https://flagcdn.com/de.svg'],
    ['cca2' => 'FR', 'nombre_comun' => 'Francia', 'capital' => 'París', 'subregion' => 'Europa', 'poblacion' => 67391582, 'bandera_png' => 'https://flagcdn.com/w320/fr.png', 'bandera_svg' => 'https://flagcdn.com/fr.svg'],
];

try {
    $db = getDB();

    $totalPaises = (int) $db->query('SELECT COUNT(*) FROM cache_paises')->fetchColumn();
    if ($totalPaises > 0) {
        $stats['paises'] = $totalPaises >= 250 ? '250+' : (string) $totalPaises;
    }

    $totalRegiones = (int) $db->query(
        "SELECT COUNT(DISTINCT region) FROM cache_paises WHERE region IS NOT NULL AND region <> ''"
    )->fetchColumn();
    if ($totalRegiones > 0) {
        $stats['regiones'] = (string) $totalRegiones;
    }

    $totalComparaciones = (int) $db->query('SELECT COUNT(*) FROM comparaciones')->fetchColumn();
    $stats['comparaciones'] = $totalComparaciones > 0 ? number_format($totalComparaciones, 0, ',', '.') : '0';

    $stmtDestacados = $db->query(
        'SELECT cca2, nombre_comun, capital, subregion, region, poblacion, bandera_png, bandera_svg
         FROM cache_paises ORDER BY poblacion DESC LIMIT 5'
    );
    $cacheDestacados = $stmtDestacados->fetchAll();

    if (!empty($cacheDestacados)) {
        $destacados = array_map(static function (array $row): array {
            return [
                'cca2'         => strtoupper((string) $row['cca2']),
                'nombre_comun' => $row['nombre_comun'],
                'capital'      => $row['capital'],
                'subregion'    => $row['subregion'] ?: $row['region'],
                'poblacion'    => (int) $row['poblacion'],
                'bandera_png'  => $row['bandera_png'],
                'bandera_svg'  => $row['bandera_svg'],
            ];
        }, $cacheDestacados);
    }
} catch (Throwable $e) {
    $dbWarning = 'Estadísticas parciales: no se pudo conectar a la base de datos.';
}

include __DIR__ . '/views/home.php';
