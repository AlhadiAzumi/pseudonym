<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    private function getRedirectUri(): string
    {
        $configured = env('GOOGLE_REDIRECT_URI');
        if ($configured && trim((string)$configured) !== '') {
            return trim((string)$configured);
        }

        return full_url('/auth/google-callback');
    }

    public function googleLogin(): void
    {
        $clientId = env('GOOGLE_CLIENT_ID') ?: Config::get('app.google.client_id');
        $redirectUri = $this->getRedirectUri();

        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;

        $params = [
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $state,
            'access_type'   => 'online',
            'prompt'        => 'select_account',
        ];

        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        header('Location: ' . $url);
        exit;
    }

    public function googleCallback(): void
    {
        // Validasi state CSRF
        if (
            empty($_GET['state']) ||
            empty($_SESSION['oauth_state']) ||
            $_GET['state'] !== $_SESSION['oauth_state']
        ) {
            die('State tidak valid atau sesi telah kedaluwarsa. Silakan coba login ulang.');
        }
        unset($_SESSION['oauth_state']);

        if (empty($_GET['code'])) {
            die('Login Google dibatalkan atau gagal.');
        }

        $clientId     = env('GOOGLE_CLIENT_ID') ?: Config::get('app.google.client_id');
        $clientSecret = env('GOOGLE_CLIENT_SECRET') ?: Config::get('app.google.client_secret');
        $redirectUri  = $this->getRedirectUri();

        // 1. Tukar authorization code dengan access token
        $tokenResponse = $this->httpPost('https://oauth2.googleapis.com/token', [
            'code'          => $_GET['code'],
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri'  => $redirectUri,
            'grant_type'    => 'authorization_code',
        ]);

        $tokenData = json_decode($tokenResponse, true);
        if (empty($tokenData['access_token'])) {
            die('Gagal mendapatkan access token dari Google. Pastikan GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET benar.');
        }

        // 2. Ambil data profil user dari Google
        $userInfoJson = $this->httpGet(
            'https://www.googleapis.com/oauth2/v3/userinfo',
            $tokenData['access_token']
        );
        $profile = json_decode($userInfoJson, true);

        if (empty($profile['sub']) || empty($profile['email'])) {
            die('Gagal mengambil profil user dari Google.');
        }

        $googleId = (string) $profile['sub'];
        $email    = (string) $profile['email'];
        $name     = (string) ($profile['name'] ?? '');
        $avatar   = (string) ($profile['picture'] ?? '');

        // 3. Cari atau buat user baru
        $user = User::findByGoogleId($googleId);

        if (!$user) {
            $user = User::findByEmail($email);
            if ($user) {
                User::updateGoogleInfo((int)$user['id'], $googleId, $name, $avatar);
                $user = User::findById((int)$user['id']);
            } else {
                $userId = User::create($email, $googleId, $name, $avatar);
                $user = User::findById($userId);
            }
        }

        $_SESSION['user_id'] = $user['id'];

        if (empty($user['username'])) {
            $this->redirect('/choose-username');
        } else {
            $this->redirect('/dashboard');
        }
    }

    public function chooseUsernameForm(): void
    {
        $user = currentUser();
        if (!$user) {
            $this->redirect('/auth/google-login');
            return;
        }

        if (!empty($user['username'])) {
            $this->redirect('/dashboard');
            return;
        }

        $this->view('choose-username', [
            'title' => 'Pilih Username — Pseudonym',
            'user'  => $user,
            'error' => $_SESSION['flash_error'] ?? null,
        ], 'none');

        unset($_SESSION['flash_error']);
    }

    public function saveUsername(): void
    {
        $user = currentUser();
        if (!$user) {
            $this->redirect('/auth/google-login');
            return;
        }

        $username = trim($_POST['username'] ?? '');

        if (!isValidUsername($username)) {
            $_SESSION['flash_error'] = 'Username harus 3-20 karakter, hanya huruf/angka/underscore.';
            $this->redirect('/choose-username');
            return;
        }

        if (User::isUsernameTaken($username, (int)$user['id'])) {
            $_SESSION['flash_error'] = 'Username sudah dipakai orang lain. Coba username yang lain.';
            $this->redirect('/choose-username');
            return;
        }

        User::updateUsername((int)$user['id'], $username);
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $this->redirect('/');
    }

    private function httpPost(string $url, array $fields): string
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
        return (string) $response;
    }

    private function httpGet(string $url, string $bearerToken): string
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
        return (string) $response;
    }
}
