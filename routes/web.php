<?php

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AboutController;

/** @var Router $router */

$router->get('/', [HomeController::class, 'index']);
$router->get('/tes', function () {
    echo "TES BERHASIL";
});
$router->get('/about', [AboutController::class, 'index']);