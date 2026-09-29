<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Message;
use App\Models\User;

class ProfileController extends Controller
{
    public function show(?string $username = null): void
    {
        if ($username === null || $username === '') {
            $username = trim($_GET['u'] ?? '');
        }

        if ($username === '') {
            $this->redirect('/');
            return;
        }

        $owner = User::findByUsername($username);

        if (!$owner) {
            http_response_code(404);
            $this->view('profile-not-found', [
                'title'    => 'Tidak Ditemukan — Pseudonym',
                'username' => $username,
            ], 'app');
            return;
        }

        $viewer = currentUser();
        $isOwner = $viewer && ((int)$viewer['id'] === (int)$owner['id']);

        $messages = Message::getPublicMessages((int)$owner['id'], (bool)$isOwner);

        $this->view('profile', [
            'title'       => '@' . $owner['username'] . ' — Pseudonym',
            'currentPage' => 'profile',
            'owner'       => $owner,
            'viewer'      => $viewer,
            'isOwner'     => $isOwner,
            'messages'    => $messages,
        ], $isOwner ? 'dashboard' : 'app');
    }
}
