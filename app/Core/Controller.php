<?php

namespace App\Core;

class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);

        require dirname(__DIR__, 2)
            . "/resources/views/{$view}.php";
    }
}