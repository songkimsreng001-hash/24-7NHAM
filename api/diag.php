<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

header('Content-Type: text/plain; charset=utf-8');

$dbUrl = getenv('DB_URL');
echo "DB_URL exists: " . ($dbUrl ? 'YES' : 'NO') . "\n";
if ($dbUrl) {
    $parsed = parse_url($dbUrl);
    echo "DB_URL user: " . ($parsed['user'] ?? '') . "\n";
    $passInUrl = $parsed['pass'] ?? '';
    echo "DB_URL pass length: " . strlen($passInUrl) . "\n";
    echo "DB_URL pass hint: " . substr($passInUrl, 0, 3) . '***' . substr($passInUrl, -3) . "\n";
    echo "DB_URL query: " . ($parsed['query'] ?? 'none') . "\n";
}

$directPass = getenv('DB_PASSWORD');
echo "DB_PASSWORD length: " . strlen($directPass) . "\n";
echo "DB_PASSWORD hint: " . substr($directPass, 0, 3) . '***' . substr($directPass, -3) . "\n";

echo "Matches: " . ($passInUrl === $directPass ? 'YES' : 'NO') . "\n";
