<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/env.php';

loadEnv(dirname(__DIR__) . '/.env');

require_once dirname(__DIR__) . '/app/Helpers/helpers.php';

use App\Core\Application;
use App\Core\Config;

final class DbSessionHandler implements SessionHandlerInterface
{
    public function open(string $path, string $name): bool { return true; }
    public function close(): bool { return true; }

    public function read(string $id): string|false
    {
        try {
            $st = getDB()->prepare('SELECT data FROM php_sessions WHERE id = :id');
            $st->execute([':id' => $id]);
            $row = $st->fetchColumn();
            return $row === false ? '' : (string) $row;
        } catch (Throwable $e) {
            error_log('session read: ' . $e->getMessage());
            return '';
        }
    }

    public function write(string $id, string $data): bool
    {
        try {
            $st = getDB()->prepare(
                'INSERT INTO php_sessions (id, data, updated_at) VALUES (:id, :data, now())
                 ON CONFLICT (id) DO UPDATE SET data = EXCLUDED.data, updated_at = now()'
            );
            return $st->execute([':id' => $id, ':data' => $data]);
        } catch (Throwable $e) {
            error_log('session write: ' . $e->getMessage());
            return false;
        }
    }

    public function destroy(string $id): bool
    {
        try {
            return getDB()->prepare('DELETE FROM php_sessions WHERE id = :id')->execute([':id' => $id]);
        } catch (Throwable $e) {
            return false;
        }
    }

    public function gc(int $max_lifetime): int|false
    {
        try {
            $st = getDB()->prepare("DELETE FROM php_sessions WHERE updated_at < now() - interval '30 days'");
            $st->execute();
            return $st->rowCount();
        } catch (Throwable $e) {
            return false;
        }
    }
}

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_set_save_handler(new DbSessionHandler(), true);
    session_set_cookie_params([
        'lifetime' => 60 * 60 * 24 * 30,
        'path'     => '/',
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

Config::load();

return new Application();