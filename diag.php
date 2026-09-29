<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

header('Content-Type: text/plain; charset=utf-8');
echo "=== DIAGNOSTIC ROUTING ===\n\n";
echo "REQUEST_URI    : " . ($_SERVER['REQUEST_URI'] ?? '(null)') . "\n";
echo "SCRIPT_NAME    : " . ($_SERVER['SCRIPT_NAME'] ?? '(null)') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? '(null)') . "\n";
echo "PHP_SELF       : " . ($_SERVER['PHP_SELF'] ?? '(null)') . "\n\n";
echo "base_url()     : " . base_url() . "\n";
echo "url('/')       : " . url('/') . "\n";
echo "url('/api/check-username') : " . url('/api/check-username') . "\n";
echo "asset('js/search-username.js') : " . asset('js/search-username.js') . "\n\n";

// Cek DB
try {
    $db = getDB();
    $stmt = $db->query("SELECT id, username FROM users LIMIT 5");
    $rows = $stmt->fetchAll();
    echo "=== DATABASE OK ===\n";
    foreach ($rows as $r) {
        echo " - #{$r['id']} @{$r['username']}\n";
    }
} catch (Throwable $e) {
    echo "=== DATABASE ERROR ===\n" . $e->getMessage() . "\n";
}
