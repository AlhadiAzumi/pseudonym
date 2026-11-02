<?php

declare(strict_types=1);

namespace App\Models;

class ContactMessage
{
    public static function getRecent(int $limit, int $offset): array
    {
        $stmt = getDB()->prepare(
            'SELECT id, name, email, category, message, created_at
             FROM contact_messages
             ORDER BY created_at DESC
             LIMIT ? OFFSET ?'
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function countAll(): int
    {
        return (int) getDB()->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
    }

    public static function create(string $name, string $email, string $category, string $message): int
    {
        $stmt = getDB()->prepare(
            'INSERT INTO contact_messages (name, email, category, message)
             VALUES (?, ?, ?, ?) RETURNING id'
        );
        $stmt->execute([$name, $email, $category, $message]);

        return (int) $stmt->fetchColumn();
    }
}
