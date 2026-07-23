<?php

use App\Core\Router;
use App\Controllers\HomeController;

/** @var Router $router */

$router->get('/', [HomeController::class, 'index']);