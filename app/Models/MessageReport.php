<?php

declare(strict_types=1);

namespace App\Models;

use PDOException;

class MessageReport
{
    public static function create(int $messageId, string $reporterHash, string $reason): bool
    {
        try {
            $stmt = getDB()->prepare(
                'INSERT INTO message_reports (message_id, reporter_hash, reason)
                 VALUES (?, ?, ?)'
            );
            return $stmt->execute([$messageId, $reporterHash, $reason]);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23505') {
                return false;
            }

            throw $exception;
        }
    }
}
