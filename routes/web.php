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

// --- SEO & Crawlers ---
$router->get('/robots.txt', function (): void {
    header('Content-Type: text/plain; charset=utf-8');
    echo "User-agent: *\nAllow: /\nDisallow: /api/\nDisallow: /dashboard\nDisallow: /choose-username\nDisallow: /auth/\n\nSitemap: " . full_url('/sitemap.xml') . "\n";
    exit;
});

$router->get('/sitemap.xml', function (): void {
    header('Content-Type: application/xml; charset=utf-8');
    $baseUrl = rtrim(full_url(), '/');
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    $pages = [
        ['uri' => '', 'priority' => '1.0', 'freq' => 'daily'],
        ['uri' => '/about', 'priority' => '0.8', 'freq' => 'monthly'],
        ['uri' => '/safety', 'priority' => '0.8', 'freq' => 'monthly'],
        ['uri' => '/contact', 'priority' => '0.7', 'freq' => 'monthly'],
    ];
    foreach ($pages as $p) {
        echo "  <url>\n";
        echo "    <loc>" . htmlspecialchars($baseUrl . $p['uri'], ENT_XML1, 'UTF-8') . "</loc>\n";
        echo "    <changefreq>" . $p['freq'] . "</changefreq>\n";
        echo "    <priority>" . $p['priority'] . "</priority>\n";
        echo "  </url>\n";
    }
    echo "</urlset>\n";
    exit;
});

// Keep this last so named application routes take precedence over usernames.
$router->get('/{username}', [ProfileController::class, 'show']);