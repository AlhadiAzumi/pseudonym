<?php
require_once __DIR__ . '/../includes/functions.php';

// Validasi state (anti CSRF)
if (
    empty($_GET['state']) ||
    empty($_SESSION['oauth_state']) ||
    $_GET['state'] !== $_SESSION['oauth_state']
) {
    die('State tidak valid. Silakan coba login ulang.');
}
unset($_SESSION['oauth_state']);

if (empty($_GET['code'])) {
    die('Login Google dibatalkan atau gagal.');
}

// 1. Tukar authorization code dengan access token
$tokenResponse = httpPost('https://oauth2.googleapis.com/token', [
    'code'          => $_GET['code'],
    'client_id'     => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'grant_type'    => 'authorization_code',
]);

$tokenData = json_decode($tokenResponse, true);
if (empty($tokenData['access_token'])) {
    die('Gagal mendapatkan token dari Google. Cek GOOGLE_CLIENT_ID / SECRET di config/config.php.');
}

// 2. Ambil data profil user dari Google
$userInfoJson = httpGet(
    'https://www.googleapis.com/oauth2/v3/userinfo',
    $tokenData['access_token']
);
$profile = json_decode($userInfoJson, true);

if (empty($profile['sub']) || empty($profile['email'])) {
    die('Gagal mengambil profil dari Google.');
}

$googleId = $profile['sub'];
$email    = $profile['email'];
$name     = $profile['name'] ?? '';
$avatar   = $profile['picture'] ?? '';

$db = getDB();

// 3. Cari user berdasarkan google_id, kalau belum ada cari berdasarkan email, kalau belum ada juga -> buat baru
$stmt = $db->prepare('SELECT * FROM users WHERE google_id = ?');
$stmt->execute([$googleId]);
$user = $stmt->fetch();

if (!$user) {
    $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Akun sudah ada tapi belum tertaut google_id -> tautkan
        $stmt = $db->prepare('UPDATE users SET google_id = ?, display_name = ?, avatar_url = ? WHERE id = ?');
        $stmt->execute([$googleId, $name, $avatar, $user['id']]);
    } else {
        // Buat akun baru, username masih NULL (diisi di choose-username.php)
        $stmt = $db->prepare('INSERT INTO users (email, google_id, display_name, avatar_url) VALUES (?, ?, ?, ?)');
        $stmt->execute([$email, $googleId, $name, $avatar]);
        $userId = $db->lastInsertId();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
    }
}

$_SESSION['user_id'] = $user['id'];

if (empty($user['username'])) {
    header('Location: ' . APP_URL . '/choose-username.php');
} else {
    header('Location: ' . APP_URL . '/dashboard.php');
}
exit;

// ---- Helper HTTP kecil (pakai cURL, tanpa dependency tambahan) ----

function httpPost(string $url, array $fields): string
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($fields),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $response = curl_exec($ch);
    if ($response === false) {
        die('cURL error: ' . curl_error($ch));
    }
    curl_close($ch);
    return $response;
}

function httpGet(string $url, string $bearerToken): string
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $bearerToken],
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $response = curl_exec($ch);
    if ($response === false) {
        die('cURL error: ' . curl_error($ch));
    }
    curl_close($ch);
    return $response;
}
