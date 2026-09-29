<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use App\Models\Message;
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

            // Anti-spam per IP per hari
            $fingerprint = senderFingerprint();
            $countToday = Message::countTodayBySender((int)$receiver['id'], $fingerprint);
            if ($countToday >= 20) {
                $this->json([
                    'success' => false,
                    'error'   => 'Kamu sudah mengirim cukup banyak pesan ke akun ini hari ini. Coba lagi besok.'
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
            $this->json(['success' => false, 'error' => 'Terjadi kesalahan pada server database.'], 500);
        }
    }
}
