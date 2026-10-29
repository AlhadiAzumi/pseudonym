<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap/app.php';

// 1. Generate random state
$state = bin2hex(random_bytes(16));

// 2. Simpan state ke COOKIE (bukan $_SESSION)
setcookie('oauth_state', $state, [
    'expires'  => time() + 600, // berlaku 10 menit
    'path'     => '/',
    'secure'   => true,         // Wajib true untuk HTTPS Vercel
    'httponly' => true,
    'samesite' => 'Lax'         // Wajib Lax untuk redirect OAuth
]);

// 3. Buat URL Redirect ke Google
$googleAuthUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id'     => GOOGLE_CLIENT_ID, // sesuaikan dengan konstanta/env kamu
    'redirect_uri'  => url('/auth/google-callback'),
    'response_type' => 'code',
    'scope'         => 'email profile',
    'state'         => $state
]);

header('Location: ' . $googleAuthUrl);
exit;