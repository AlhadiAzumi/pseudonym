<?php

require_once __DIR__ . '/config/database.php';

echo "<pre>";

echo "DB_HOST : " . env('DB_HOST') . PHP_EOL;
echo "DB_NAME : " . env('DB_NAME') . PHP_EOL;
echo "DB_USER : " . env('DB_USER') . PHP_EOL;
echo "DB_PASS : " . env('DB_PASS') . PHP_EOL;

echo "\n";

try {
    getDB();
    echo "✅ Database Connected";
} catch (Exception $e) {
    echo "❌ " . $e->getMessage();
}