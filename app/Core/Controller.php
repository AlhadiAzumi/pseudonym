<?php

declare(strict_types=1);

namespace App\Core;

class Controller
{
    protected function view(
        string $view,
        array $data = [],
        string $layout = 'app'
    ): void {
        extract($data);

        $content = dirname(__DIR__, 2) . "/resources/views/{$view}.php";

        if ($layout === '' || $layout === 'none') {
            require $content;
            return;
        }

        require dirname(__DIR__, 2) . "/resources/views/layouts/{$layout}.php";
    }

    protected function json(array $data, int $status = 200): void
    {
        jsonResponse($data, $status);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }
}