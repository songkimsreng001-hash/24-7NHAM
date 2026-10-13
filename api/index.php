<?php

// Forward Vercel requests to normal Laravel public/index.php
// Ensure /tmp directory paths for serverless write permissions

$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_EVENTS_CACHE=/tmp/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');

// SQLite support in serverless: copy project database to writable /tmp if using sqlite
$dbSource = __DIR__ . '/../database/database.sqlite';
$dbTarget = '/tmp/database.sqlite';
if (!file_exists($dbTarget)) {
    if (file_exists($dbSource)) {
        @copy($dbSource, $dbTarget);
    } else {
        @touch($dbTarget);
    }
}
if (getenv('DB_CONNECTION') === 'sqlite' || !getenv('DB_CONNECTION')) {
    putenv("DB_DATABASE={$dbTarget}");
    $_ENV['DB_DATABASE'] = $dbTarget;
    $_SERVER['DB_DATABASE'] = $dbTarget;
}

require __DIR__ . '/../public/index.php';

