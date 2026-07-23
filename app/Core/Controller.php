<?php

namespace App\Core;

class Controller
{
    protected function view(
        string $view,
        array $data = [],
        string $layout = 'app'
    ): void {

        extract($data);

        $content = dirname(__DIR__, 2)
            . "/resources/views/{$view}.php";

        require dirname(__DIR__, 2)
            . "/resources/views/layouts/{$layout}.php";
    }
}