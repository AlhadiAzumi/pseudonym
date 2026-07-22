<?php
/**
 * Konfigurasi umum aplikasi + kredensial Google OAuth.
 *
 * PENTING: Isi GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET dengan punya kamu
 * dari Google Cloud Console. Lihat README.md bagian "Setup Google OAuth"
 * untuk panduan langkah demi langkah.
 */

// --- Ganti sesuai folder project kamu di htdocs/laragon ---
// Contoh: kalau project ada di htdocs/rahasia-app, maka APP_URL = http://localhost/rahasia-app
define('APP_URL', 'http://localhost/rahasia-app');

// --- Kredensial Google OAuth (dari Google Cloud Console) ---
define('GOOGLE_CLIENT_ID', '347057146711-p9eok1u3uo0d8fgbc9f57r6nlmelee6g.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', APP_URL . '/auth/google-callback.php');

// Batas panjang pesan & balasan
define('MAX_MESSAGE_LENGTH', 500);
define('MAX_REPLY_LENGTH', 500);

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Jakarta');
