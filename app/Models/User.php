<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class User
{
    public static function findById(int $id): ?array
    {
        $stmt = getDB()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function findByUsername(string $username): ?array
    {
        $stmt = getDB()->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function findByGoogleId(string $googleId): ?array
    {
        $stmt = getDB()->prepare('SELECT * FROM users WHERE google_id = ?');
        $stmt->execute([$googleId]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = getDB()->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function create(string $email, string $googleId, string $displayName, string $avatarUrl): int
    {
        $db = getDB();
        $stmt = $db->prepare('INSERT INTO users (email, google_id, display_name, avatar_url) VALUES (?, ?, ?, ?) RETURNING id');
        $stmt->execute([$email, $googleId, $displayName, $avatarUrl]);

        return (int) $stmt->fetchColumn();
    }

    public static function updateGoogleInfo(int $id, string $googleId, string $displayName, string $avatarUrl): bool
    {
        $stmt = getDB()->prepare('UPDATE users SET google_id = ?, display_name = ?, avatar_url = ? WHERE id = ?');
        return $stmt->execute([$googleId, $displayName, $avatarUrl, $id]);
    }

    public static function updateUsername(int $id, string $username): bool
    {
        $stmt = getDB()->prepare('UPDATE users SET username = ? WHERE id = ?');
        return $stmt->execute([$username, $id]);
    }

    public static function isUsernameTaken(string $username, ?int $exceptUserId = null): bool
    {
        if ($exceptUserId) {
            $stmt = getDB()->prepare('SELECT id FROM users WHERE username = ? AND id != ?');
            $stmt->execute([$username, $exceptUserId]);
        } else {
            $stmt = getDB()->prepare('SELECT id FROM users WHERE username = ?');
            $stmt->execute([$username]);
        }

        return (bool) $stmt->fetch();
    }
}
