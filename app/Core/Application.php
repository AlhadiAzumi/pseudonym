<?php

declare(strict_types=1);

namespace App\Core;

if (getenv('VERCEL') !== false) {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED);
}

class Application
{
    protected Router $router;
    protected Request $request;
    protected Response $response;

    public function __construct()
    {
        $this->router = new Router();
        $this->request = new Request();
        $this->response = new Response();

        $router = $this->router;

        require dirname(__DIR__, 2) . '/routes/web.php';
    }

    public function run(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        // Deteksi dan potong subfolder dasar secara otomatis
        $onVercel = getenv('VERCEL') !== false || isset($_SERVER['VERCEL']);
        $base = $onVercel ? '' : base_url('');
        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }

        // Hapus /index.php jika ada pada URL
        if (str_starts_with($uri, '/index.php')) {
            $uri = substr($uri, strlen('/index.php'));
        }

        $uri = '/' . trim($uri, '/');
        if ($uri === '//') {
            $uri = '/';
        }

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $this->router->dispatch($method, $uri);
    }
}