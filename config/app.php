<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'Pseudonym'),
    'url'  => env('APP_URL', full_url()),
    'timezone' => env('APP_TIMEZONE', 'Asia/Jakarta'),
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID', '347057146711-p9eok1u3uo0d8fgbc9f57r6nlmelee6g.apps.googleusercontent.com'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', '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'),
        'redirect_uri'  => env('GOOGLE_REDIRECT_URI', full_url('/auth/google-callback')),
    ],
    'max_message_length' => 500,
    'max_reply_length'   => 500,
];