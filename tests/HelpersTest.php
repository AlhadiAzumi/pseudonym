<?php

declare(strict_types=1);

namespace Tests;

use App\Core\Router;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    private array $originalServer;

    protected function setUp(): void
    {
        $this->originalServer = $_SERVER;
        unset(
            $_SERVER['VERCEL'],
            $_SERVER['HTTP_X_VERCEL_FORWARDED_FOR'],
            $_SERVER['HTTP_X_REAL_IP'],
            $_SERVER['HTTP_X_FORWARDED_FOR']
        );
        putenv('VERCEL');
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->originalServer;
    }

    #[DataProvider('usernameCases')]
    public function testValidatesUsernames(string $username, bool $expected): void
    {
        self::assertSame($expected, isValidUsername($username));
    }

    public static function usernameCases(): array
    {
        return [
            'minimum length' => ['abc', true],
            'maximum length' => [str_repeat('a', 20), true],
            'underscore and digits' => ['user_123', true],
            'too short' => ['ab', false],
            'too long' => [str_repeat('a', 21), false],
            'invalid punctuation' => ['bad-name', false],
        ];
    }

    public function testSenderFingerprintUsesRemoteAddressInsteadOfUntrustedForwardedFor(): void
    {
        $_SERVER['REMOTE_ADDR'] = '192.0.2.10';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '198.51.100.15';
        $salt = (string) \App\Core\Config::get('app.fingerprint_salt', '');

        self::assertSame(
            hash_hmac('sha256', '192.0.2.10|' . date('Y-m-d'), $salt),
            senderFingerprint()
        );
    }

    public function testSenderFingerprintUsesVercelForwardedAddressOnVercel(): void
    {
        $_SERVER['VERCEL'] = '1';
        $_SERVER['REMOTE_ADDR'] = '192.0.2.10';
        $_SERVER['HTTP_X_VERCEL_FORWARDED_FOR'] = '198.51.100.15, 10.0.0.1';

        $fingerprint = senderFingerprint();
        $_SERVER['HTTP_X_VERCEL_FORWARDED_FOR'] = '203.0.113.20';

        self::assertNotSame($fingerprint, senderFingerprint());
    }

    public function testPostRoutesRejectCrossOriginRequests(): void
    {
        $_SERVER['HTTP_HOST'] = 'pseudonym.example';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_ORIGIN'] = 'https://attacker.example';
        $called = false;
        $router = new Router();
        $router->post('/submit', static function () use (&$called): void {
            $called = true;
        });

        ob_start();
        $router->dispatch('POST', '/submit');
        $response = ob_get_clean();

        self::assertSame(403, http_response_code());
        self::assertSame('Permintaan ditolak. Muat ulang halaman dan coba lagi.', $response);
        self::assertFalse($called);
        http_response_code(200);
    }

    public function testPostRoutesAcceptSameOriginRequests(): void
    {
        $_SERVER['HTTP_HOST'] = 'pseudonym.example';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_ORIGIN'] = 'https://pseudonym.example';
        $_SESSION['csrf_token'] = 'expected-token';
        $_SERVER['HTTP_X_CSRF_TOKEN'] = 'expected-token';
        $called = false;
        $router = new Router();
        $router->post('/submit', static function () use (&$called): void {
            $called = true;
        });

        $router->dispatch('POST', '/submit');

        self::assertTrue($called);
    }

    public function testPostRoutesRejectMissingCsrfToken(): void
    {
        $_SERVER['HTTP_HOST'] = 'pseudonym.example';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_ORIGIN'] = 'https://pseudonym.example';
        unset($_SERVER['HTTP_X_CSRF_TOKEN']);
        $_SESSION['csrf_token'] = 'expected-token';
        $called = false;
        $router = new Router();
        $router->post('/submit', static function () use (&$called): void {
            $called = true;
        });

        ob_start();
        $router->dispatch('POST', '/submit');
        $response = ob_get_clean();

        self::assertSame(403, http_response_code());
        self::assertSame('Permintaan ditolak. Muat ulang halaman dan coba lagi.', $response);
        self::assertFalse($called);
        http_response_code(200);
    }

    public function testPostRoutesAcceptValidTokenWhenOriginHeaderIsUnavailable(): void
    {
        $_SERVER['HTTP_HOST'] = 'pseudonym.example';
        $_SERVER['HTTPS'] = 'on';
        unset($_SERVER['HTTP_ORIGIN'], $_SERVER['HTTP_REFERER']);
        $_SESSION['csrf_token'] = 'expected-token';
        $_SERVER['HTTP_X_CSRF_TOKEN'] = 'expected-token';
        $called = false;
        $router = new Router();
        $router->post('/submit', static function () use (&$called): void {
            $called = true;
        });

        $router->dispatch('POST', '/submit');

        self::assertTrue($called);
    }

    public function testAssetUrlIncludesContentVersion(): void
    {
        self::assertMatchesRegularExpression(
            '/\?v=[a-f0-9]{16}$/',
            asset('css/style.css')
        );
    }

    public function testAdminAccessIsRestrictedToConfiguredGoogleEmail(): void
    {
        $hadAdminEmail = array_key_exists('ADMIN_EMAIL', $_ENV);
        $previousAdminEmail = $_ENV['ADMIN_EMAIL'] ?? null;

        try {
            $_ENV['ADMIN_EMAIL'] = 'Admin@example.com';
            \App\Core\Config::load();

            self::assertTrue(isAdminUser(['email' => 'admin@example.com']));
            self::assertFalse(isAdminUser(['email' => 'someone-else@example.com']));
            self::assertFalse(isAdminUser(null));

            $_ENV['ADMIN_EMAIL'] = '';
            \App\Core\Config::load();
            self::assertFalse(isAdminUser(['email' => 'admin@example.com']));
        } finally {
            if ($hadAdminEmail) {
                $_ENV['ADMIN_EMAIL'] = $previousAdminEmail;
            } else {
                unset($_ENV['ADMIN_EMAIL']);
            }
            \App\Core\Config::load();
        }
    }
}
