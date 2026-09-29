<?php

declare(strict_types=1);

namespace App\Core;

class Config
{
    protected static array $items = [];

    public static function load(): void
    {
        $configPath = dirname(__DIR__, 2) . '/config';

        if (file_exists($configPath . '/app.php')) {
            self::$items['app'] = require $configPath . '/app.php';
        }

        if (file_exists($configPath . '/database.php')) {
            self::$items['database'] = require $configPath . '/database.php';
        }
    }

    public static function get(string $key, $default = null)
    {
        $segments = explode('.', $key);
        $data = self::$items;

        foreach ($segments as $segment) {
            if (!is_array($data) || !array_key_exists($segment, $data)) {
                return $default;
            }
            $data = $data[$segment];
        }

        return $data;
    }
}