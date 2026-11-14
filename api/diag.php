<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

header('Content-Type: text/plain; charset=utf-8');

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->useStoragePath('/tmp/storage');
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Testing Laravel DB Connection with Fixed Config ---\n";
try {
    $mysqlConf = config('database.connections.mysql');
    unset($mysqlConf['password']);
    $mysqlConf['password_len'] = strlen(config('database.connections.mysql.password'));
    print_r($mysqlConf);

    $cats = \Illuminate\Support\Facades\DB::select('SELECT COUNT(*) as cnt FROM categories');
    echo "SUCCESS! Category count in TiDB: " . $cats[0]->cnt . "\n";
    $prods = \Illuminate\Support\Facades\DB::select('SELECT COUNT(*) as cnt FROM products');
    echo "SUCCESS! Product count in TiDB: " . $prods[0]->cnt . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
