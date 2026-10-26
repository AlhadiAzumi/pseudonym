<?php

declare(strict_types=1);

return [
    'host'     => env('DB_HOST', 'localhost'),
    'port'     => env('DB_PORT', '5432'),
    'database' => env('DB_NAME', 'postgres'),
    'username' => env('DB_USER', 'postgres'),
    'password' => env('DB_PASS', ''),
    'sslmode'  => env('DB_SSLMODE', 'require'),
];