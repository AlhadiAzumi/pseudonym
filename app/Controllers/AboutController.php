<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class AboutController extends Controller
{
    public function index(): void
    {
        $user = currentUser();

        $this->view('about', [
            'title'       => 'About — Pseudonym',
            'currentPage' => 'about',
            'user'        => $user,
        ]);
    }
}