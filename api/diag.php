<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

header('Content-Type: text/plain; charset=utf-8');
echo "HELLO FROM DIAG\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "DB_HOST: " . getenv('DB_HOST') . "\n";
echo "DB_PORT: " . getenv('DB_PORT') . "\n";
echo "DB_DATABASE: " . getenv('DB_DATABASE') . "\n";
echo "DB_USERNAME: " . getenv('DB_USERNAME') . "\n";

echo "\n--- Testing PDO Connection from Vercel ---\n";
try {
    $caFunction = function () {
        $ca = getenv('MYSQL_ATTR_SSL_CA');
        if ($ca && file_exists($ca)) return $ca;
        foreach ([
            '/etc/pki/tls/certs/ca-bundle.crt',
            '/etc/ssl/certs/ca-bundle.crt',
            '/etc/ssl/certs/ca-certificates.crt',
        ] as $path) {
            if (file_exists($path)) return $path;
        }
        return '';
    };

    $caPath = $caFunction();
    echo "Using CA Path: " . ($caPath ?: '(empty string)') . "\n";

    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT') ?: '4000';
    $db = getenv('DB_DATABASE');
    $user = getenv('DB_USERNAME');
    $pass = getenv('DB_PASSWORD');

    $options = [
        PDO::MYSQL_ATTR_SSL_CA => $caPath,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];

    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db}", $user, $pass, $options);
    echo "SUCCESS: Connected to TiDB from Vercel!\n";
    $userInfo = $pdo->query("SELECT USER(), CURRENT_USER()")->fetch(PDO::FETCH_ASSOC);
    print_r($userInfo);

    // Also test Laravel DB facade!
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->useStoragePath('/tmp/storage');
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "\n--- Testing Laravel DB Connection ---\n";
    $cats = \Illuminate\Support\Facades\DB::select('SELECT COUNT(*) as cnt FROM categories');
    echo "Laravel DB Success! Category count: " . $cats[0]->cnt . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
