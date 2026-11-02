<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'Pseudonym'),
    'url'  => env('APP_URL', full_url()),
    'timezone' => env('APP_TIMEZONE', 'Asia/Jakarta'),
    'admin_email' => trim((string) env('ADMIN_EMAIL', '')),
    'google' => [
        // Jangan pernah menaruh kredensial asli sebagai fallback di repo publik.
        'client_id'     => env('GOOGLE_CLIENT_ID', ''),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
        'redirect_uri'  => env('GOOGLE_REDIRECT_URI', full_url('/auth/google-callback')),
    ],
    'max_message_length' => 500,
    'max_reply_length'   => 500,

    // Anti-spam / rate limit
    'max_messages_per_day_per_sender' => (int) env('MAX_MESSAGES_PER_DAY', 20),
    'max_messages_per_hour_per_sender' => (int) env('MAX_MESSAGES_PER_HOUR', 10),
    'max_messages_per_hour_per_target' => (int) env('MAX_MESSAGES_TARGET_PER_HOUR', 60),
    'fingerprint_salt' => env('APP_FINGERPRINT_SALT', 'pseudonym-fingerprint-v1'),
];