<?php

declare(strict_types=1);

if (!defined('APP_URL')) {
    define('APP_URL', full_url());
}

if (!defined('GOOGLE_CLIENT_ID')) {
    define('GOOGLE_CLIENT_ID', env('GOOGLE_CLIENT_ID', '347057146711-p9eok1u3uo0d8fgbc9f57r6nlmelee6g.apps.googleusercontent.com'));
}

if (!defined('GOOGLE_CLIENT_SECRET')) {
    define('GOOGLE_CLIENT_SECRET', env('GOOGLE_CLIENT_SECRET', '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'));
}

if (!defined('GOOGLE_REDIRECT_URI')) {
    define('GOOGLE_REDIRECT_URI', full_url('/auth/google-callback'));
}

if (!defined('MAX_MESSAGE_LENGTH')) {
    define('MAX_MESSAGE_LENGTH', 500);
}

if (!defined('MAX_REPLY_LENGTH')) {
    define('MAX_REPLY_LENGTH', 500);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Jakarta');
