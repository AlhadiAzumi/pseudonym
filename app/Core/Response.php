<?php

namespace App\Core;

class Response
{
    public function setStatusCode(int $code): void
    {
        http_response_code($code);
    }

    public function json(array $data): void
    {
        header('Content-Type: application/json');

        echo json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }

    public function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}