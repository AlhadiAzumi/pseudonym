<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', 'php://stderr');
error_reporting(E_ALL);

try {
    require dirname(__DIR__) . '/index.php';
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain');
    echo get_class($e) . "\n" . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine();
}