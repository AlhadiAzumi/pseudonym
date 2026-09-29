<?php

declare(strict_types=1);

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AboutController;
use App\Controllers\SafetyController;
use App\Controllers\ContactController;
use App\Controllers\DashboardController;
use App\Controllers\ProfileController;
use App\Controllers\AuthController;
use App\Controllers\ApiController;

/** @var Router $router */

// --- Web Pages ---
$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [AboutController::class, 'index']);
$router->get('/safety', [SafetyController::class, 'index']);
$router->get('/contact', [ContactController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/u/{username}', [ProfileController::class, 'show']);
$router->get('/u', [ProfileController::class, 'show']);

// --- Auth & Onboarding ---
$router->get('/auth/google-login', [AuthController::class, 'googleLogin']);
$router->get('/auth/google-login.php', [AuthController::class, 'googleLogin']);
$router->get('/auth/google-callback', [AuthController::class, 'googleCallback']);
$router->get('/auth/google-callback.php', [AuthController::class, 'googleCallback']);
$router->get('/auth/logout', [AuthController::class, 'logout']);
$router->get('/auth/logout.php', [AuthController::class, 'logout']);
$router->get('/choose-username', [AuthController::class, 'chooseUsernameForm']);
$router->post('/choose-username', [AuthController::class, 'saveUsername']);

// --- REST API Endpoints ---
$router->get('/api/check-username', [ApiController::class, 'checkUsername']);
$router->get('/api/check-username.php', [ApiController::class, 'checkUsername']);
$router->post('/api/send-message', [ApiController::class, 'sendMessage']);
$router->post('/api/send-message.php', [ApiController::class, 'sendMessage']);
$router->post('/api/reply-message', [ApiController::class, 'replyMessage']);
$router->post('/api/reply-message.php', [ApiController::class, 'replyMessage']);
$router->post('/api/toggle-visibility', [ApiController::class, 'toggleVisibility']);
$router->post('/api/toggle-message-visibility.php', [ApiController::class, 'toggleVisibility']);