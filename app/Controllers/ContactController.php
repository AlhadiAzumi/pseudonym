<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class ContactController extends Controller
{
    public function index()
    {
        $user = currentUser();

        return $this->view('contact', [
            'title'       => 'Contact Us - Pseudonym',
            'currentPage' => 'contact',
            'user'        => $user
        ]);
    }
}