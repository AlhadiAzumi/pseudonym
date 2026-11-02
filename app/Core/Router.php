<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    /**
     * @var array<string, array<int, array{pattern: string, regex: string, paramNames: array<string>, handler: mixed}>>
     */
    protected array $routes = [
        'GET'  => [],
        'POST' => [],
    ];

    public function get(string $uri, mixed $handler): void
    {
        $this->addRoute('GET', $uri, $handler);
    }

    public function post(string $uri, mixed $handler): void
    {
        $this->addRoute('POST', $uri, $handler);
    }

    protected function addRoute(string $method, string $uri, mixed $handler): void
    {
        $uri = '/' . trim($uri, '/');
        if ($uri === '//') {
            $uri = '/';
        }

        // Cari parameter {paramName}
        preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_-]*)\}/', $uri, $paramMatches);
        $paramNames = $paramMatches[1] ?? [];

        // Konversi ke format regex
        $regex = preg_replace('/\{[a-zA-Z_][a-zA-Z0-9_-]*\}/', '([^/]+)', $uri);
        $regex = '#^' . $regex . '$#';

        $this->routes[$method][] = [
            'pattern'    => $uri,
            'regex'      => $regex,
            'paramNames' => $paramNames,
            'handler'    => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $uri = '/' . trim($uri, '/');
        if ($uri === '//') {
            $uri = '/';
        }

        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $route) {
            if (preg_match($route['regex'], $uri, $matches)) {
                if ($method === 'POST' && !$this->hasSameOrigin()) {
                    $this->rejectPost($uri);
                    return;
                }

                if ($method === 'POST' && !isValidCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? null)) {
                    $this->rejectPost($uri);
                    return;
                }

                array_shift($matches); // Hilangkan full match
                $params = array_map('urldecode', $matches);

                $handler = $route['handler'];

                if (is_array($handler)) {
                    [$controllerClass, $actionMethod] = $handler;
                    $controller = new $controllerClass();
                    $controller->$actionMethod(...$params);
                    return;
                }

                if (is_callable($handler)) {
                    $handler(...$params);
                    return;
                }
            }
        }

        // 404 Handler
        http_response_code(404);
        if (str_starts_with($uri, '/api/')) {
            jsonResponse(['success' => false, 'error' => 'Endpoint tidak ditemukan.'], 404);
        }

        echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>404 - Halaman Tidak Ditemukan</title>';
        echo '<link rel="stylesheet" href="' . asset('css/style.css') . '"></head><body>';
        echo '<div style="min-height:100vh; display:flex; align-items:center; justify-content:center; background:var(--dark-bg); color:var(--text-light); text-align:center; font-family:sans-serif; padding:20px;">';
        echo '<div><h1 style="font-size:4rem; margin-bottom:10px; color:var(--primary);">404</h1>';
        echo '<p style="font-size:1.2rem; margin-bottom:24px; color:var(--text-muted);">Halaman yang kamu cari tidak ditemukan.</p>';
        echo '<a href="' . url('/') . '" style="background:var(--primary); color:var(--dark-bg); padding:10px 24px; border-radius:8px; text-decoration:none; font-weight:700;">Kembali ke Beranda</a>';
        echo '</div></div></body></html>';
    }

    private function hasSameOrigin(): bool
    {
        $source = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '';
        if ($source === '') {
            return true;
        }

        $sourceParts = is_string($source) ? parse_url($source) : false;
        $requestHost = (string) ($_SERVER['HTTP_HOST'] ?? '');
        $requestParts = parse_url('//' . $requestHost);

        if (
            !is_array($sourceParts) ||
            !isset($sourceParts['scheme'], $sourceParts['host']) ||
            !is_array($requestParts) ||
            !isset($requestParts['host'])
        ) {
            return false;
        }

        $requestScheme = (
            (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') ||
            strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https' ||
            getenv('VERCEL') !== false ||
            isset($_SERVER['VERCEL'])
        ) ? 'https' : 'http';
        $sourceScheme = strtolower($sourceParts['scheme']);
        $sourcePort = $sourceParts['port'] ?? ($sourceScheme === 'https' ? 443 : 80);
        $requestPort = $requestParts['port'] ?? ($requestScheme === 'https' ? 443 : 80);

        return in_array($sourceScheme, ['http', 'https'], true)
            && $sourceScheme === $requestScheme
            && strtolower($sourceParts['host']) === strtolower($requestParts['host'])
            && $sourcePort === $requestPort;
    }

    private function rejectPost(string $uri): void
    {
        http_response_code(403);
        if (str_starts_with($uri, '/api/')) {
            jsonResponse(['success' => false, 'error' => 'Permintaan ditolak. Muat ulang halaman dan coba lagi.'], 403);
        }

        header('Content-Type: text/plain; charset=utf-8');
        echo 'Permintaan ditolak. Muat ulang halaman dan coba lagi.';
    }
}