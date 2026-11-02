<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Message;
use PHPUnit\Framework\TestCase;
use PDO;

final class MessageTest extends TestCase
{
    public function testCountTodayBySenderCountsMessagesWithinReceiverInbox(): void
    {
        $databaseName = getenv('TEST_DB_NAME');
        if ($databaseName === false || $databaseName === '') {
            self::markTestSkipped('Set TEST_DB_* variables to run database integration tests.');
        }

        $databaseVariables = [
            'DB_HOST' => 'TEST_DB_HOST',
            'DB_PORT' => 'TEST_DB_PORT',
            'DB_NAME' => 'TEST_DB_NAME',
            'DB_USER' => 'TEST_DB_USER',
            'DB_PASS' => 'TEST_DB_PASS',
            'DB_SSLMODE' => 'TEST_DB_SSLMODE',
        ];
        foreach ($databaseVariables as $appKey => $testKey) {
            $value = getenv($testKey);
            if ($value !== false) {
                $_ENV[$appKey] = $value;
                putenv($appKey . '=' . $value);
            }
        }

        $db = getDB();
        self::assertInstanceOf(PDO::class, $db);
        $db->beginTransaction();

        try {
            $unique = bin2hex(random_bytes(8));
            $stmt = $db->prepare(
                'INSERT INTO users (email, google_id) VALUES (?, ?) RETURNING id'
            );
            $stmt->execute([$unique . '@example.test', $unique]);
            $receiverId = (int) $stmt->fetchColumn();
            $senderHash = hash('sha256', $unique);

            $stmt = $db->prepare(
                'INSERT INTO messages (receiver_id, content, sender_hash) VALUES (?, ?, ?)'
            );
            $stmt->execute([$receiverId, 'Test message', $senderHash]);

            self::assertSame(1, Message::countTodayBySender($receiverId, $senderHash));
        } finally {
            $db->rollBack();
        }
    }
}
