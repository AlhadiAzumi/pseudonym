<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Config;

class HomeController extends Controller
{
    public function index(): void
    {
        $user = currentUser();

        $this->view('home', [
            'title'       => Config::get('app.name', 'Pseudonym') . ' — Kirim Pesan Rahasia Anonim',
            'currentPage' => 'home',
            'user'        => $user,
        ]);
    }
}