<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap/app.php';

$getState    = $_GET['state'] ?? '';
$cookieState = $_COOKIE['oauth_state'] ?? '';

// 1. Validasi State dari Cookie vs GET Parameter dari Google
if (empty($getState) || empty($cookieState) || !hash_equals($cookieState, $getState)) {
    die('State tidak valid atau sesi telah kedaluwarsa. Silakan coba login ulang.');
}

// 2. Hapus cookie state setelah sukses divalidasi
setcookie('oauth_state', '', time() - 3600, '/');

// 3. Ambil 'code' dan lanjutkan proses tukar token dengan Google & simpan ke Supabase
$code = $_GET['code'] ?? '';
if (empty($code)) {
    die('Authorization code tidak ditemukan.');
}

// ... Lanjutkan kode tukar token Google & query ke Supabase ...