<?php

// Ensure all temporary folders for Laravel exist in /tmp (read-only filesystem workaround)
$dirs = [
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Redirect storage and compilation paths to /tmp
putenv('APP_STORAGE=/tmp/storage');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['APP_STORAGE'] = '/tmp/storage';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['APP_STORAGE'] = '/tmp/storage';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// Direct cache manifests to writable /tmp
$cacheConfigs = [
    'APP_CONFIG_CACHE' => '/tmp/bootstrap/cache/config.php',
    'APP_EVENTS_CACHE' => '/tmp/bootstrap/cache/events.php',
    'APP_PACKAGES_CACHE' => '/tmp/bootstrap/cache/packages.php',
    'APP_ROUTES_CACHE' => '/tmp/bootstrap/cache/routes.php',
    'APP_SERVICES_CACHE' => '/tmp/bootstrap/cache/services.php',
];

foreach ($cacheConfigs as $key => $path) {
    putenv("{$key}={$path}");
    $_ENV[$key] = $path;
    $_SERVER[$key] = $path;
}

// Safe fallback for APP_KEY if not configured in Vercel dashboard yet
if (empty(getenv('APP_KEY')) && empty($_ENV['APP_KEY'])) {
    $fallbackKey = 'base64:zEL0FTe3UhYP6rx8UjiBWnukytLBlZoShHxtDgu/ynw=';
    putenv("APP_KEY={$fallbackKey}");
    $_ENV['APP_KEY'] = $fallbackKey;
    $_SERVER['APP_KEY'] = $fallbackKey;
}

// Safe database fallback: If DB_HOST is 127.0.0.1 or localhost on Vercel, fallback to SQLite
// so the application loads without a PDO 2002 Connection Refused error
if (getenv('VERCEL') || getenv('NOW_REGION')) {
    $dbHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? '127.0.0.1');
    $dbConnection = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? 'mysql');

    if (in_array($dbHost, ['127.0.0.1', 'localhost']) && $dbConnection !== 'sqlite') {
        $sqliteFile = '/tmp/database.sqlite';
        if (!file_exists($sqliteFile)) {
            @touch($sqliteFile);
        }
        putenv('DB_CONNECTION=sqlite');
        putenv("DB_DATABASE={$sqliteFile}");
        $_ENV['DB_CONNECTION'] = 'sqlite';
        $_ENV['DB_DATABASE'] = $sqliteFile;
        $_SERVER['DB_CONNECTION'] = 'sqlite';
        $_SERVER['DB_DATABASE'] = $sqliteFile;
    }
}

require __DIR__ . '/../public/index.php';
