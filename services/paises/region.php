<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/helpers.php';

requireMethod('GET');

$region = strtolower(trim((string) ($_GET['region'] ?? '')));
$regionesValidas = ['africa', 'americas', 'asia', 'europe', 'oceania', 'antarctic'];

if ($region === '') {
    jsonError('Parámetro region requerido');
}

if (!in_array($region, $regionesValidas, true)) {
    jsonError('Región no válida. Use: ' . implode(', ', $regionesValidas));
}

try {
    $map = [
        'africa' => 'Africa',
        'americas' => 'Americas',
        'asia' => 'Asia',
        'europe' => 'Europe',
        'oceania' => 'Oceania',
        'antarctic' => 'Polar'
    ];
    $dbRegion = $map[$region] ?? ucfirst($region);

    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM cache_paises WHERE region = :region ORDER BY nombre_comun ASC');
    $stmt->execute([':region' => $dbRegion]);
    $rows = $stmt->fetchAll();

    if (!empty($rows)) {
        $paises = array_map(static function (array $row): array {
            return resumirPaisRegion(cacheRowToCountry($row));
        }, $rows);
    } else {
        $results = fetchRestCountries('/region/' . rawurlencode($region));
        $normalizados = normalizeCountries($results);
        upsertCacheCountries($normalizados);
        $paises = array_map('resumirPaisRegion', $normalizados);
    }

    jsonSuccess([
        'region' => $region,
        'total'  => count($paises),
        'paises' => $paises,
    ]);
} catch (Throwable $e) {
    jsonError('Error al obtener países por región: ' . $e->getMessage(), 500);
}
