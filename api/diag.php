<?php
header('Content-Type: text/plain; charset=utf-8');
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown') . "\n";
echo "Dir: " . __DIR__ . "\n";
echo "Vendor Autoload: " . (file_exists(__DIR__ . '/../vendor/autoload.php') ? 'YES' : 'NO') . "\n";
echo "Extensions: " . implode(', ', get_loaded_extensions()) . "\n";
