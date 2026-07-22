<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Pastikan kolom is_hidden ada pada database yang sudah ada.
try {
    $db = getDB();
        $db->exec("ALTER TABLE messages ADD COLUMN IF NOT EXISTS is_hidden TINYINT(1) NOT NULL DEFAULT 0");
        } catch (Throwable $e) {
            // Abaikan bila migrasi tidak bisa dijalankan; halaman tetap berjalan.
            }

            /** Ambil user yang sedang login, atau null kalau belum login */
            function currentUser(): ?array
            {
                if (!empty($_SESSION['user_id'])) {
                        $stmt = getDB()->prepare('SELECT * FROM users WHERE id = ?');
                                $stmt->execute([$_SESSION['user_id']]);
                                        $user = $stmt->fetch();
                                                return $user ?: null;
                                                    }
                                                        return null;
                                                        }

                                                        /** Redirect ke login kalau belum login */
                                                        function requireLogin(): array
                                                        {
                                                            $user = currentUser();
                                                                if (!$user) {
                                                                        header('Location: ' . APP_URL . '/auth/google-login.php');
                                                                                exit;
                                                                                    }
                                                                                        // Kalau belum punya username, paksa lengkapi profil dulu
                                                                                            if (empty($user['username']) && basename($_SERVER['SCRIPT_NAME']) !== 'choose-username.php') {
                                                                                                    header('Location: ' . APP_URL . '/choose-username.php');
                                                                                                            exit;
                                                                                                                }
                                                                                                                    return $user;
                                                                                                                    }

                                                                                                                    /** Bersihkan output HTML supaya aman dari XSS */
                                                                                                                    function h(string $str): string
                                                                                                                    {
                                                                                                                        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
                                                                                                                        }

                                                                                                                        /** Kirim response JSON lalu berhenti */
                                                                                                                        function jsonResponse(array $data, int $statusCode = 200): void
                                                                                                                        {
                                                                                                                            http_response_code($statusCode);
                                                                                                                                header('Content-Type: application/json');
                                                                                                                                    echo json_encode($data);
                                                                                                                                        exit;
                                                                                                                                        }

                                                                                                                                        /** Ambil body JSON dari request POST */
                                                                                                                                        function jsonInput(): array
                                                                                                                                        {
                                                                                                                                            $raw = file_get_contents('php://input');
                                                                                                                                                $data = json_decode($raw, true);
                                                                                                                                                    return is_array($data) ? $data : [];
                                                                                                                                                    }

                                                                                                                                                    /** Validasi format username: huruf/angka/underscore, 3-20 karakter */
                                                                                                                                                    function isValidUsername(string $username): bool
                                                                                                                                                    {
                                                                                                                                                        return (bool) preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
                                                                                                                                                        }

                                                                                                                                                        /** Hash sederhana untuk anti-spam (IP + tanggal), bukan untuk identitas */
                                                                                                                                                        function senderFingerprint(): string
                                                                                                                                                        {
                                                                                                                                                            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                                                                                                                                                                return hash('sha256', $ip . date('Y-m-d'));
                                                                                                                                                                }

                                                                                                                                                                /** Format waktu relatif sederhana ala "5 menit lalu" */
                                                                                                                                                                function timeAgo(string $datetime): string
                                                                                                                                                                {
                                                                                                                                                                    $diff = time() - strtotime($datetime);
                                                                                                                                                                        if ($diff < 60) return 'baru saja';
                                                                                                                                                                            if ($diff < 3600) return floor($diff / 60) . ' menit lalu';
                                                                                                                                                                                if ($diff < 86400) return floor($diff / 3600) . ' jam lalu';
                                                                                                                                                                                    if ($diff < 2592000) return floor($diff / 86400) . ' hari lalu';
                                                                                                                                                                                        return date('d M Y', strtotime($datetime));
                                                                                                                                                                                        }
                                                                                                                                                                                        