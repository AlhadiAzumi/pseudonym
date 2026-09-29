<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap/app.php';

$u = trim($_GET['u'] ?? '');
if ($u !== '') {
    header('Location: ' . url('/u/' . urlencode($u)));
} else {
    header('Location: ' . url('/'));
}
exit;