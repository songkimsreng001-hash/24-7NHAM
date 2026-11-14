<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Ensure required storage directories exist in /tmp
$dirs = [
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

try {
    // Register autoload
    require __DIR__ . '/../vendor/autoload.php';

    // Bootstrap Laravel
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Fix read-only filesystem on Vercel Serverless
    $app->useStoragePath('/tmp/storage');

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Serverless Execution Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "Stack Trace:\n" . $e->getTraceAsString();
}
