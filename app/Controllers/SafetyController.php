<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class SafetyController extends Controller
{
    public function index()
    {
        $user = currentUser();

        return $this->view('safety', [
            'title'       => 'Safety - Pseudonym',
            'currentPage' => 'safety',
            'user'        => $user
        ]);
    }
}