<?php

namespace App\Core;

class Application
{
    protected Router $router;

    public function __construct()
    {
        $this->router = new Router();

        $router = $this->router;

        require dirname(__DIR__, 2) . '/routes/web.php';
    }

public function run(): void
{
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $base = '/pseudonym';

    if (str_starts_with($uri, $base)) {
        $uri = substr($uri, strlen($base));
    }

    if ($uri === '' || $uri === '/index.php') {
        $uri = '/';
    }

    $this->router->dispatch(
        $_SERVER['REQUEST_METHOD'],
        $uri
    );
}
}