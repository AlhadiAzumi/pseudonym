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
            // Di Vercel aplikasi selalu ada di root domain
            if (getenv('VERCEL') !== false || isset($_SERVER['VERCEL'])) {
                $base = '';
            } else {
                $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
                if (str_ends_with($scriptName, '/api/index.php')) {
                    $base = '';
        } else {
            $scriptDir = dirname($scriptName);
            $base = ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim($scriptDir, '/');
        }
    }
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
        $appUrl = trim((string) env('APP_URL', ''));
        if ($appUrl !== '') {
            return rtrim($appUrl, '/') . ($path === '' ? '' : '/' . ltrim($path, '/'));
        }

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
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $assetsPath = str_ends_with($scriptName, '/api/index.php') ? '/assets/' : '/assets/';
        $relativePath = rawurldecode(ltrim($path, '/'));
        $assetRoot = realpath(dirname(__DIR__, 2) . '/public/assets');
        $assetFile = $assetRoot === false ? false : realpath($assetRoot . DIRECTORY_SEPARATOR . $relativePath);
        $version = '';

        if (
            $assetRoot !== false &&
            $assetFile !== false &&
            str_starts_with($assetFile, $assetRoot . DIRECTORY_SEPARATOR) &&
            is_file($assetFile)
        ) {
            $hash = hash_file('sha256', $assetFile);
            if ($hash !== false) {
                $version = '?v=' . substr($hash, 0, 16);
            }
        }

        return base_url($assetsPath . ltrim($path, '/') . $version);
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
            $port     = env('DB_PORT', '5432');
            $database = env('DB_NAME', 'postgres');
            $username = env('DB_USER', 'postgres');
            $password = env('DB_PASS', '');
            $sslmode  = env('DB_SSLMODE', 'require');

            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s;sslmode=%s;connect_timeout=5',
                $host, $port, $database, $sslmode
            );

            $pdo = new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => true,
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
            } catch (Throwable $exception) {
                error_log(sprintf(
                    'currentUser lookup failed: %s: %s in %s:%d',
                    get_class($exception),
                    $exception->getMessage(),
                    $exception->getFile(),
                    $exception->getLine()
                ));
                return null;
            }
        }

        return null;
    }
}

if (!function_exists('isAdminUser')) {
    function isAdminUser(?array $user): bool
    {
        $adminEmail = strtolower(trim((string) Config::get('app.admin_email', '')));
        $userEmail = strtolower(trim((string) ($user['email'] ?? '')));

        return $adminEmail !== ''
            && filter_var($adminEmail, FILTER_VALIDATE_EMAIL) !== false
            && $userEmail !== ''
            && hash_equals($adminEmail, $userEmail);
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

if (!function_exists('csrfToken')) {
    function csrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('isValidCsrfToken')) {
    function isValidCsrfToken(mixed $token): bool
    {
        $expected = $_SESSION['csrf_token'] ?? null;

        return is_string($expected)
            && $expected !== ''
            && is_string($token)
            && hash_equals($expected, $token);
    }
}

if (!function_exists('clientIp')) {
    /**
     * Ambil IP klien yang bisa dipercaya.
     *
     * Header forwarding hanya dipercaya saat request berjalan di Vercel, yang
     * menetapkan X-Vercel-Forwarded-For. Di hosting lain REMOTE_ADDR adalah
     * satu-satunya sumber yang dipercaya.
     */
    function clientIp(): string
    {
        $remote = trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));
        $isVercel = getenv('VERCEL') !== false || isset($_SERVER['VERCEL']);

        if ($isVercel) {
            foreach (['HTTP_X_VERCEL_FORWARDED_FOR', 'HTTP_X_REAL_IP'] as $header) {
                $value = (string) ($_SERVER[$header] ?? '');
                foreach (explode(',', $value) as $part) {
                    $ip = trim($part);
                    if ($ip !== '' && filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }

        if ($remote !== '' && filter_var($remote, FILTER_VALIDATE_IP) !== false) {
            return $remote;
        }

        return 'unknown';
    }
}

if (!function_exists('senderFingerprint')) {
    /**
     * Hash anti-spam per pengirim (IP + tanggal), di-HMAC dengan salt server
     * supaya hash tidak bisa di-reverse atau dipalsukan dari luar.
     */
    function senderFingerprint(): string
    {
        $salt = (string) Config::get('app.fingerprint_salt', 'pseudonym-fingerprint-v1');

        return hash_hmac('sha256', clientIp() . '|' . date('Y-m-d'), $salt);
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
