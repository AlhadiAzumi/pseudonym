<?php

declare(strict_types=1);

namespace App\Models;

class ContactMessage
{
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
