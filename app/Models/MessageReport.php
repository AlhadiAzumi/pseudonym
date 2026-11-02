<?php

declare(strict_types=1);

namespace App\Models;

use PDOException;

class MessageReport
{
    public static function getRecent(int $limit, int $offset): array
    {
        $stmt = getDB()->prepare(
            'SELECT
                mr.id,
                mr.message_id,
                mr.reason,
                mr.status,
                mr.created_at,
                m.content AS message_content,
                m.is_hidden,
                u.username AS receiver_username
             FROM message_reports mr
             JOIN messages m ON m.id = mr.message_id
             JOIN users u ON u.id = m.receiver_id
             ORDER BY CASE mr.status WHEN \'pending\' THEN 0 WHEN \'reviewed\' THEN 1 ELSE 2 END,
                      mr.created_at DESC
             LIMIT ? OFFSET ?'
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function countAll(): int
    {
        return (int) getDB()->query('SELECT COUNT(*) FROM message_reports')->fetchColumn();
    }

    public static function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['reviewed', 'dismissed'], true)) {
            return false;
        }

        $stmt = getDB()->prepare('UPDATE message_reports SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);

        return $stmt->rowCount() > 0;
    }

    public static function deleteById(int $id): bool
    {
        $stmt = getDB()->prepare('DELETE FROM message_reports WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    public static function hideReportedMessage(int $reportId): bool
    {
        $db = getDB();
        $ownsTransaction = !$db->inTransaction();
        if ($ownsTransaction) {
            $db->beginTransaction();
        }

        try {
            $hideMessage = $db->prepare(
                'UPDATE messages
                 SET is_hidden = TRUE
                 WHERE id = (SELECT message_id FROM message_reports WHERE id = ?)
                 RETURNING id'
            );
            $hideMessage->execute([$reportId]);

            if ($hideMessage->fetchColumn() === false) {
                if ($ownsTransaction) {
                    $db->rollBack();
                }
                return false;
            }

            $markReviewed = $db->prepare("UPDATE message_reports SET status = 'reviewed' WHERE id = ?");
            $markReviewed->execute([$reportId]);
            if ($ownsTransaction) {
                $db->commit();
            }

            return true;
        } catch (\Throwable $exception) {
            if ($ownsTransaction && $db->inTransaction()) {
                $db->rollBack();
            }

            throw $exception;
        }
    }

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
