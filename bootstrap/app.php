<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/env.php';

// Muat .env jika tersedia
loadEnv(dirname(__DIR__) . '/.env');

// Muat helper fungsi global
require_once dirname(__DIR__) . '/app/Helpers/helpers.php';

use App\Core\Application;
use App\Core\Config;

// Load konfigurasi aplikasi
Config::load();

// Jalankan instance aplikasi
return new Application();