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
}