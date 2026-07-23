<?php

namespace App\Core;

class Router
{
    /**
     * @var array<string, mixed>
     */
    protected array $routes = [];

    public function get(string $uri, mixed $handler): void
    {
        $this->routes["GET:$uri"] = $handler;
    }

    public function dispatch(string $method, string $uri): void
{
    $key = "$method:$uri";

    if (!isset($this->routes[$key])) {
        http_response_code(404);
        echo "404 Not Found";
        return;
    }

    $handler = $this->routes[$key];

    if (is_array($handler)) {

        [$controller, $action] = $handler;

        $controller = new $controller();

        $controller->$action();

        return;
    }

    $handler();
}
}