<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap/app.php';

$query = $_SERVER['QUERY_STRING'] ?? '';
$target = url('/auth/google-callback') . ($query !== '' ? '?' . $query : '');

header('Location: ' . $target);
exit;
