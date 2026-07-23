<?php

namespace App\Core;

class Router
{
    /**
     * @var array<string, callable>
     */
    protected array $routes = [];

    public function get(string $uri, callable $callback): void
    {
        $this->routes["GET:$uri"] = $callback;
    }

    public function dispatch(string $method, string $uri): void
    {
        $key = "$method:$uri";

        if (!isset($this->routes[$key])) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        call_user_func($this->routes[$key]);
    }
}