<?php

use App\Core\Router;

/** @var Router $router */

$router->get('/', function () {
    echo "<h1>🏠 Welcome to Pseudonym</h1>";
});

$router->get('/about', function () {
    echo "<h1>About Page</h1>";
});