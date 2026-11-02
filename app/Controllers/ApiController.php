<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use App\Models\Message;
use App\Models\MessageReport;
use App\Models\Reply;
use App\Models\User;
use Throwable;

class ApiController extends Controller
{
    public function checkUsername(): void
    {
        $username = trim($_GET['u'] ?? '');

        if (!isValidUsername($username)) {
            $this->json(['exists' => false]);
            return;
        }

        try {
            $user = User::findByUsername($username);

            if (!$user) {
                $this->json(['exists' => false]);
                return;
            }

            $this->json([
                'exists'       => true,
                'username'     => $user['username'],
                'display_name' => $user['display_name'] ?: $user['username'],
                'avatar_url'   => $user['avatar_url'],
            ]);
        } catch (Throwable $e) {
            $this->logFailure('checkUsername', $e);
            $this->json([
                'exists' => false,
                'error'  => 'Database belum terkoneksi atau sedang offline.',
            ], 500);
        }
    }

    public function sendMessage(): void
    {
        $input = jsonInput();
        $username = trim((string)($input['username'] ?? ''));
        $content  = trim((string)($input['content'] ?? ''));

        if ($username === '' || $content === '') {
            $this->json(['success' => false, 'error' => 'Username dan isi pesan wajib diisi.'], 400);
            return;
        }

        $maxLength = (int) Config::get('app.max_message_length', 500);
        if (mb_strlen($content) > $maxLength) {
            $this->json(['success' => false, 'error' => 'Pesan terlalu panjang (maks ' . $maxLength . ' karakter).'], 400);
            return;
        }

        try {
            $receiver = User::findByUsername($username);
            if (!$receiver) {
                $this->json(['success' => false, 'error' => 'Akun tujuan tidak ditemukan.'], 404);
                return;
            }

            // Anti-spam per IP globally per hari
            $fingerprint = senderFingerprint();
            $maxDay = (int) Config::get('app.max_messages_per_day_per_sender', 20);
            $countToday = Message::countTodayGloballyBySender($fingerprint);
            if ($countToday >= $maxDay) {
                $this->json([
                    'success' => false,
                    'error'   => 'Kamu sudah mencapai batas pesan harian. Coba lagi besok.'
                ], 429);
                return;
            }

            // Anti-spam per jam per pengirim
            $maxHour = (int) Config::get('app.max_messages_per_hour_per_sender', 10);
            $countHour = Message::countSinceBySender($fingerprint, 1);
            if ($countHour >= $maxHour) {
                $this->json([
                    'success' => false,
                    'error'   => 'Terlalu banyak pesan dalam satu jam. Coba lagi nanti.'
                ], 429);
                return;
            }

            // Anti-spam per jam per tujuan (menghindari pencurian banyak akun sekaligus)
            $maxTargetHour = (int) Config::get('app.max_messages_per_hour_per_target', 60);
            $countTargetHour = Message::countSinceByReceiver((int)$receiver['id'], 1);
            if ($countTargetHour >= $maxTargetHour) {
                $this->json([
                    'success' => false,
                    'error'   => 'Akun ini sedang menerima terlalu banyak pesan. Coba lagi nanti.'
                ], 429);
                return;
            }

            $messageId = Message::create((int)$receiver['id'], $content, $fingerprint);

            $this->json([
                'success' => true,
                'message' => [
                    'id'         => $messageId,
                    'content'    => $content,
                    'created_at' => 'baru saja',
                ],
            ]);
        } catch (Throwable $e) {
            $this->logFailure('sendMessage', $e);
            $this->json(['success' => false, 'error' => 'Terjadi kesalahan pada server database.'], 500);
        }
    }

    public function replyMessage(): void
    {
        $user = currentUser();
        if (!$user) {
            $this->json(['success' => false, 'error' => 'Kamu harus login untuk membalas pesan.'], 401);
            return;
        }

        $input = jsonInput();
        $messageId = (int)($input['message_id'] ?? 0);
        $content   = trim((string)($input['content'] ?? ''));

        if ($messageId <= 0 || $content === '') {
            $this->json(['success' => false, 'error' => 'Data tidak lengkap.'], 400);
            return;
        }

        $maxLength = (int) Config::get('app.max_reply_length', 500);
        if (mb_strlen($content) > $maxLength) {
            $this->json(['success' => false, 'error' => 'Balasan terlalu panjang (maks ' . $maxLength . ' karakter).'], 400);
            return;
        }

        try {
            $message = Message::findById($messageId);
            if (!$message || (int)$message['receiver_id'] !== (int)$user['id']) {
                $this->json(['success' => false, 'error' => 'Pesan tidak ditemukan atau bukan milikmu.'], 403);
                return;
            }

            $existingReply = Reply::findByMessageId($messageId);
            if ($existingReply) {
                $this->json(['success' => false, 'error' => 'Pesan ini sudah dibalas sebelumnya.'], 409);
                return;
            }

            Reply::create($messageId, $content);

            $this->json([
                'success' => true,
                'reply'   => [
                    'content'    => $content,
                    'created_at' => 'baru saja',
                ],
            ]);
        } catch (Throwable $e) {
            $this->logFailure('replyMessage', $e);
            $this->json(['success' => false, 'error' => 'Terjadi kesalahan pada server database.'], 500);
        }
    }

    public function toggleVisibility(): void
    {
        $user = currentUser();
        if (!$user) {
            $this->json(['success' => false, 'error' => 'Kamu harus login untuk mengubah visibilitas pesan.'], 401);
            return;
        }

        $input = jsonInput();
        $messageId = (int)($input['message_id'] ?? 0);
        $hidden    = (bool)($input['hidden'] ?? false);

        if ($messageId <= 0) {
            $this->json(['success' => false, 'error' => 'Data tidak lengkap.'], 400);
            return;
        }

        try {
            $message = Message::findById($messageId);
            if (!$message || (int)$message['receiver_id'] !== (int)$user['id']) {
                $this->json(['success' => false, 'error' => 'Pesan tidak ditemukan atau kamu tidak memiliki akses.'], 403);
                return;
            }

            Message::toggleVisibility($messageId, $hidden, (int)$user['id']);

            $this->json([
                'success' => true,
                'hidden'  => $hidden,
            ]);
        } catch (Throwable $e) {
            $this->logFailure('toggleVisibility', $e);
            $this->json(['success' => false, 'error' => 'Terjadi kesalahan pada server database.'], 500);
        }
    }

    public function deleteMessage(): void
    {
        $user = currentUser();
        if (!$user) {
            $this->json(['success' => false, 'error' => 'Kamu harus login untuk menghapus pesan.'], 401);
            return;
        }

        $input = jsonInput();
        $messageId = (int)($input['message_id'] ?? 0);
        if ($messageId <= 0) {
            $this->json(['success' => false, 'error' => 'Pesan tidak valid.'], 400);
            return;
        }

        try {
            if (!Message::deleteById($messageId, (int)$user['id'])) {
                $this->json(['success' => false, 'error' => 'Pesan tidak ditemukan atau bukan milikmu.'], 404);
                return;
            }

            $this->json(['success' => true]);
        } catch (Throwable $e) {
            $this->logFailure('deleteMessage', $e);
            $this->json(['success' => false, 'error' => 'Terjadi kesalahan saat menghapus pesan.'], 500);
        }
    }

    public function reportMessage(): void
    {
        $input = jsonInput();
        $messageId = (int)($input['message_id'] ?? 0);
        $reasonInput = $input['reason'] ?? '';
        $reason = is_string($reasonInput) ? trim($reasonInput) : '';
        $allowedReasons = ['harassment', 'hate_speech', 'personal_info', 'threat', 'spam', 'other'];

        if ($messageId <= 0 || !in_array($reason, $allowedReasons, true)) {
            $this->json(['success' => false, 'error' => 'Pilih alasan laporan yang valid.'], 400);
            return;
        }

        try {
            $message = Message::findById($messageId);
            $isHidden = $message && in_array($message['is_hidden'], [true, 1, '1', 't', 'true'], true);
            if (!$message || $isHidden) {
                $this->json(['success' => false, 'error' => 'Pesan publik tidak ditemukan.'], 404);
                return;
            }

            $user = currentUser();
            if ($user && (int)$message['receiver_id'] === (int)$user['id']) {
                $this->json(['success' => false, 'error' => 'Kamu tidak dapat melaporkan pesan di kotak masukmu sendiri.'], 403);
                return;
            }

            if (!MessageReport::create($messageId, senderFingerprint(), $reason)) {
                $this->json(['success' => false, 'error' => 'Kamu sudah melaporkan pesan ini.'], 409);
                return;
            }

            $this->json(['success' => true]);
        } catch (Throwable $e) {
            $this->logFailure('reportMessage', $e);
            $this->json(['success' => false, 'error' => 'Laporan belum berhasil disimpan. Coba lagi nanti.'], 500);
        }
    }

    private function logFailure(string $operation, Throwable $exception): void
    {
        error_log(sprintf(
            'API %s failed: %s: %s in %s:%d',
            $operation,
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        ));
    }
}
