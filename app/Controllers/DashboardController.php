<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Message;

class DashboardController extends Controller
{
    public function index(): void
    {
        // Pastikan user sudah login
        $user = requireLogin();

        // Ambil data inbox dan statistik
        $stats = Message::getStats((int)$user['id']);

        // Link profile untuk dibagikan
        $profileUrl = full_url('/' . rawurlencode($user['username']));

        // Kirim data ke view dengan layout dashboard
        $this->view('dashboard', [
            'title'       => 'Kotak Masuk — Pseudonym',
            'currentPage' => 'dashboard',
            'user'        => $user,
            'messages'    => $stats['messages'],
            'total'       => $stats['total'],
            'replied'     => $stats['replied'],
            'waiting'     => $stats['waiting'],
            'profileUrl'  => $profileUrl,
        ], 'dashboard');
    }
}