<?php

declare(strict_types=1);

/**
 * Utilidades compartidas para vistas PHP.
 */

function appBaseUrl(): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = str_replace('\\', '/', dirname($script));

    if ($dir === '/' || $dir === '.') {
        return '';
    }

    return rtrim($dir, '/');
}

function assetUrl(string $path): string
{
    return appBaseUrl() . '/assets/' . ltrim($path, '/');
}

function pageUrl(string $page): string
{
    return appBaseUrl() . '/' . ltrim($page, '/');
}

function serviceUrl(string $path): string
{
    return appBaseUrl() . '/services/' . ltrim($path, '/');
}

function esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function formatNumber(int $number): string
{
    return number_format($number, 0, ',', '.');
}
