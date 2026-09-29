<?php

declare(strict_types=1);

use App\Core\Config;
use App\Models\User;

if (!function_exists('env')) {
    /**
     * Mengambil nilai dari environment variable
     */
    function env(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false || $value === null) {
            return $default;
        }

        return match (strtolower((string) $value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'null', '(null)' => null,
            'empty', '(empty)' => '',
            default => $value,
        };
    }
}

if (!function_exists('base_url')) {
    /**
     * Menghasilkan URL relatif dasar proyek (misal: /pseudonym atau kosong jika di root)
     */
    function base_url(string $path = ''): string
    {
        static $base = null;

        if ($base === null) {
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $scriptDir = dirname($scriptName);
            $base = ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim(str_replace('\\', '/', $scriptDir), '/');
        }

        $cleanPath = '/' . ltrim($path, '/');
        if ($cleanPath === '/') {
            return $base !== '' ? $base : '/';
        }

        return $base . $cleanPath;
    }
}

if (!function_exists('url')) {
    /**
     * Alias untuk base_url
     */
    function url(string $path = ''): string
    {
        return base_url($path);
    }
}

if (!function_exists('full_url')) {
    /**
     * Menghasilkan URL lengkap termasuk scheme dan host (http://localhost/pseudonym/path)
     */
    function full_url(string $path = ''): string
    {
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || 
                  (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        return $scheme . '://' . $host . base_url($path);
    }
}

if (!function_exists('asset')) {
    /**
     * Menghasilkan URL aset statis
     */
    function asset(string $path = ''): string
    {
        return base_url('/public/assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('h')) {
    /**
     * Sanitasi output HTML untuk mencegah XSS
     */
    function h(?string $str): string
    {
        return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('getDB')) {
    /**
     * Mengambil instance PDO Database (Singleton)
     */
    function getDB(): PDO
    {
        static $pdo = null;

        if ($pdo === null) {
            $host     = env('DB_HOST', 'localhost');
            $database = env('DB_NAME', 'rahasia_app');
            $username = env('DB_USER', 'root');
            $password = env('DB_PASS', '');
            $charset  = 'utf8mb4';

            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $host, $database, $charset);

            $pdo = new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }

        return $pdo;
    }
}

if (!function_exists('currentUser')) {
    /**
     * Mengambil data user yang sedang login
     */
    function currentUser(): ?array
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!empty($_SESSION['user_id'])) {
            try {
                return User::findById((int)$_SESSION['user_id']);
            } catch (Throwable) {
                return null;
            }
        }

        return null;
    }
}

if (!function_exists('requireLogin')) {
    /**
     * Memastikan user sudah login, redirect jika belum
     */
    function requireLogin(): array
    {
        $user = currentUser();

        if (!$user) {
            header('Location: ' . url('/auth/google-login'));
            exit;
        }

        if (empty($user['username'])) {
            $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
            if (!str_ends_with($currentUri, '/choose-username')) {
                header('Location: ' . url('/choose-username'));
                exit;
            }
        }

        return $user;
    }
}

if (!function_exists('jsonResponse')) {
    /**
     * Mengirim response JSON dan menghentikan script
     */
    function jsonResponse(array $data, int $statusCode = 200): void
    {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

if (!function_exists('jsonInput')) {
    /**
     * Membaca request body JSON
     */
    function jsonInput(): array
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (is_array($data)) {
            return $data;
        }

        return $_POST;
    }
}

if (!function_exists('isValidUsername')) {
    /**
     * Validasi format username: huruf, angka, underscore (3-20 karakter)
     */
    function isValidUsername(string $username): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
    }
}

if (!function_exists('senderFingerprint')) {
    /**
     * Hash anti-spam harian per IP
     */
    function senderFingerprint(): string
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        return hash('sha256', $ip . date('Y-m-d'));
    }
}

if (!function_exists('timeAgo')) {
    /**
     * Format waktu relatif Bahasa Indonesia
     */
    function timeAgo(?string $datetime): string
    {
        if (!$datetime) {
            return 'baru saja';
        }

        $diff = time() - strtotime($datetime);
        if ($diff < 60) {
            return 'baru saja';
        }
        if ($diff < 3600) {
            return floor($diff / 60) . ' menit lalu';
        }
        if ($diff < 86400) {
            return floor($diff / 3600) . ' jam lalu';
        }
        if ($diff < 2592000) {
            return floor($diff / 86400) . ' hari lalu';
        }

        return date('d M Y', strtotime($datetime));
    }
}
