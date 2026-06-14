<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../paises/helpers.php';

requireMethod('POST');

$input = getJsonInput();
$cca2List = $input['paises'] ?? $input['cca2'] ?? [];

if (!is_array($cca2List)) {
    $cca2List = array_filter(array_map('trim', explode(',', (string) $cca2List)));
}

$cca2List = array_values(array_unique(array_map(static function ($code) {
    return strtoupper(trim((string) $code));
}, $cca2List)));

$cca2List = array_filter($cca2List);

if (count($cca2List) < 2) {
    jsonError('Debe enviar al menos 2 códigos cca2');
}

if (count($cca2List) > 6) {
    jsonError('Máximo 6 países por comparación');
}

try {
    $paises = [];

    foreach ($cca2List as $cca2) {
        $pais = getCachePaisByCca2($cca2);

        if ($pais === null) {
            jsonError('País no encontrado: ' . $cca2, 404);
        }

        $paises[] = $pais;
    }

    jsonSuccess([
        'total'  => count($paises),
        'paises' => $paises,
    ]);
} catch (Throwable $e) {
    jsonError('Error al comparar países: ' . $e->getMessage(), 500);
}
