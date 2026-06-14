<?php

declare(strict_types=1);

const REST_COUNTRIES_BASE = 'https://restcountries.com/v3.1';
const FLAG_CDN_BASE = 'https://flagcdn.com';

/**
 * @return array<int, array<string, mixed>>
 */
function fetchRestCountries(string $path): array
{
    $url = REST_COUNTRIES_BASE . $path;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ]);

    $response = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        throw new RuntimeException('Error al conectar con REST Countries: ' . $curlError);
    }

    if ($httpCode === 404) {
        return [];
    }

    if ($httpCode >= 400) {
        throw new RuntimeException('REST Countries respondió con código ' . $httpCode);
    }

    $data = json_decode($response, true);

    if (!is_array($data)) {
        throw new RuntimeException('Respuesta inválida de REST Countries');
    }

    if (isset($data['status']) && (int) $data['status'] >= 400) {
        return [];
    }

    return $data;
}

/**
 * @param array<string, mixed> $country
 * @return array<string, mixed>
 */
function normalizeCountry(array $country): array
{
    $cca2 = strtolower((string) ($country['cca2'] ?? ''));
    $capital = $country['capital'][0] ?? null;

    $languages = [];
    if (!empty($country['languages']) && is_array($country['languages'])) {
        $languages = array_values($country['languages']);
    }

    $currencies = [];
    if (!empty($country['currencies']) && is_array($country['currencies'])) {
        foreach ($country['currencies'] as $code => $info) {
            $currencies[] = [
                'codigo'  => $code,
                'nombre'  => $info['name'] ?? $code,
                'simbolo' => $info['symbol'] ?? '',
            ];
        }
    }

    $borders = $country['borders'] ?? [];
    if (!is_array($borders)) {
        $borders = [];
    }

    return [
        'cca2'           => strtoupper($cca2),
        'cca3'           => $country['cca3'] ?? '',
        'nombre_comun'   => $country['name']['common'] ?? '',
        'nombre_oficial' => $country['name']['official'] ?? '',
        'capital'        => $capital,
        'region'         => $country['region'] ?? '',
        'subregion'      => $country['subregion'] ?? '',
        'poblacion'      => (int) ($country['population'] ?? 0),
        'area_km2'       => $country['area'] ?? null,
        'bandera_svg'    => FLAG_CDN_BASE . '/' . $cca2 . '.svg',
        'bandera_png'    => FLAG_CDN_BASE . '/w320/' . $cca2 . '.png',
        'idiomas'        => $languages,
        'monedas'        => $currencies,
        'fronteras'      => $borders,
        'lat'            => $country['latlng'][0] ?? null,
        'lng'            => $country['latlng'][1] ?? null,
    ];
}

/**
 * @param array<int, array<string, mixed>> $countries
 * @return array<int, array<string, mixed>>
 */
function normalizeCountries(array $countries): array
{
    $normalized = [];

    foreach ($countries as $country) {
        if (!is_array($country) || empty($country['cca2'])) {
            continue;
        }
        $normalized[] = normalizeCountry($country);
    }

    usort($normalized, static fn(array $a, array $b): int => strcmp($a['nombre_comun'], $b['nombre_comun']));

    return $normalized;
}

/**
 * @param array<string, mixed> $row
 */
function cacheRowToCountry(array $row, bool $resumido = false): array
{
    $idiomas = json_decode((string) ($row['idiomas'] ?? '[]'), true) ?: [];
    $monedas = json_decode((string) ($row['monedas'] ?? '[]'), true) ?: [];
    $fronteras = json_decode((string) ($row['fronteras'] ?? '[]'), true) ?: [];

    $pais = [
        'cca2'         => strtoupper((string) $row['cca2']),
        'nombre_comun' => $row['nombre_comun'] ?? '',
        'capital'      => $row['capital'] ?? null,
        'region'       => $row['region'] ?? '',
        'poblacion'    => (int) ($row['poblacion'] ?? 0),
        'bandera_svg'  => $row['bandera_svg'] ?? (FLAG_CDN_BASE . '/' . strtolower((string) $row['cca2']) . '.svg'),
    ];

    if ($resumido) {
        return $pais;
    }

    return array_merge($pais, [
        'cca3'           => $row['cca3'] ?? '',
        'nombre_oficial' => $row['nombre_oficial'] ?? '',
        'subregion'      => $row['subregion'] ?? '',
        'area_km2'       => $row['area_km2'] ?? null,
        'bandera_png'    => $row['bandera_png'] ?? (FLAG_CDN_BASE . '/w320/' . strtolower((string) $row['cca2']) . '.png'),
        'idiomas'        => $idiomas,
        'monedas'        => $monedas,
        'fronteras'      => $fronteras,
        'lat'            => $row['lat'] ?? null,
        'lng'            => $row['lng'] ?? null,
    ]);
}

/**
 * @return array<int, array<string, mixed>>
 */
function searchCachePaises(string $termino): array
{
    $db = getDB();
    $like = '%' . $termino . '%';
    $stmt = $db->prepare(
        'SELECT * FROM cache_paises
         WHERE nombre_comun LIKE :q1 OR capital LIKE :q2
         ORDER BY nombre_comun ASC
         LIMIT 50'
    );
    $stmt->execute([':q1' => $like, ':q2' => $like]);

    $paises = [];
    foreach ($stmt->fetchAll() as $row) {
        $paises[] = cacheRowToCountry($row);
    }

    return $paises;
}

function getCachePaisByCca2(string $cca2): ?array
{
    $db = getDB();
    $code = strtoupper(trim($cca2));
    if (strlen($code) === 2) {
        $stmt = $db->prepare('SELECT * FROM cache_paises WHERE cca2 = :code LIMIT 1');
    } elseif (strlen($code) === 3) {
        $stmt = $db->prepare('SELECT * FROM cache_paises WHERE cca3 = :code LIMIT 1');
    } else {
        return null;
    }
    $stmt->execute([':code' => $code]);
    $row = $stmt->fetch();

    return $row ? cacheRowToCountry($row, false) : null;
}

/**
 * @param array<int, array<string, mixed>> $countries
 */
function upsertCacheCountries(array $countries): void
{
    if (empty($countries)) {
        return;
    }

    $db = getDB();
    $sql = 'INSERT INTO cache_paises
        (cca2, cca3, nombre_comun, nombre_oficial, capital, region, subregion,
         poblacion, area_km2, bandera_svg, bandera_png, idiomas, monedas, fronteras, lat, lng)
        VALUES
        (:cca2, :cca3, :nombre_comun, :nombre_oficial, :capital, :region, :subregion,
         :poblacion, :area_km2, :bandera_svg, :bandera_png, :idiomas, :monedas, :fronteras, :lat, :lng)
        ON DUPLICATE KEY UPDATE
        cca3 = VALUES(cca3),
        nombre_comun = VALUES(nombre_comun),
        nombre_oficial = VALUES(nombre_oficial),
        capital = VALUES(capital),
        region = VALUES(region),
        subregion = VALUES(subregion),
        poblacion = VALUES(poblacion),
        area_km2 = VALUES(area_km2),
        bandera_svg = VALUES(bandera_svg),
        bandera_png = VALUES(bandera_png),
        idiomas = VALUES(idiomas),
        monedas = VALUES(monedas),
        fronteras = VALUES(fronteras),
        lat = VALUES(lat),
        lng = VALUES(lng)';

    $stmt = $db->prepare($sql);

    foreach ($countries as $pais) {
        $stmt->execute([
            ':cca2'           => $pais['cca2'],
            ':cca3'           => $pais['cca3'],
            ':nombre_comun'   => $pais['nombre_comun'],
            ':nombre_oficial' => $pais['nombre_oficial'],
            ':capital'        => $pais['capital'],
            ':region'         => $pais['region'],
            ':subregion'      => $pais['subregion'],
            ':poblacion'      => $pais['poblacion'],
            ':area_km2'       => $pais['area_km2'],
            ':bandera_svg'    => $pais['bandera_svg'],
            ':bandera_png'    => $pais['bandera_png'],
            ':idiomas'        => json_encode($pais['idiomas'], JSON_UNESCAPED_UNICODE),
            ':monedas'        => json_encode($pais['monedas'], JSON_UNESCAPED_UNICODE),
            ':fronteras'      => json_encode($pais['fronteras'], JSON_UNESCAPED_UNICODE),
            ':lat'            => $pais['lat'],
            ':lng'            => $pais['lng'],
        ]);
    }
}

function registrarHistorialBusqueda(string $termino, string $tipo, int $resultados): void
{
    try {
        $db = getDB();
        $stmt = $db->prepare(
            'INSERT INTO historial_busquedas (usuario_id, termino, tipo, resultados, ip_address)
             VALUES (:usuario_id, :termino, :tipo, :resultados, :ip)'
        );
        $stmt->execute([
            ':usuario_id'  => getSessionUserId(),
            ':termino'     => $termino,
            ':tipo'        => $tipo,
            ':resultados'  => $resultados,
            ':ip'          => getClientIp(),
        ]);
    } catch (Throwable $e) {
        // El historial no debe bloquear la búsqueda.
    }
}

function resumirPaisBusqueda(array $pais): array
{
    return [
        'cca2'         => $pais['cca2'],
        'nombre_comun' => $pais['nombre_comun'],
        'capital'      => $pais['capital'] ?? null,
        'region'       => $pais['region'] ?? '',
        'poblacion'    => $pais['poblacion'] ?? 0,
        'area_km2'     => $pais['area_km2'] ?? null,
        'bandera_svg'  => $pais['bandera_svg'] ?? '',
        'idiomas'      => $pais['idiomas'] ?? [],
        'monedas'      => $pais['monedas'] ?? [],
    ];
}

function resumirPaisRegion(array $pais): array
{
    return [
        'cca2'         => $pais['cca2'],
        'nombre_comun' => $pais['nombre_comun'],
        'capital'      => $pais['capital'] ?? null,
        'poblacion'    => $pais['poblacion'] ?? 0,
        'bandera_svg'  => $pais['bandera_svg'] ?? '',
    ];
}
