<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ContactMessage;
use PDOException;

class ContactController extends Controller
{
    public function index()
    {
        $user = currentUser();
        $success = $_SESSION['contact_success'] ?? null;
        $error = $_SESSION['contact_error'] ?? null;
        $oldInput = $_SESSION['contact_old_input'] ?? [];

        unset(
            $_SESSION['contact_success'],
            $_SESSION['contact_error'],
            $_SESSION['contact_old_input']
        );

        $this->view('contact', [
            'title'       => 'Contact us',
            'currentPage' => 'contact',
            'user'        => $user,
            'success'     => $success,
            'error'       => $error,
            'oldInput'    => $oldInput,
        ]);
    }

    public function submit(): void
    {
        $input = static fn(string $key): string =>
            isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';

        $name = $input('name');
        $email = $input('email');
        $category = $input('category');
        $message = $input('message');
        $categories = [
            'general' => 'Pertanyaan Umum',
            'bug' => 'Laporan Masalah / Bug',
            'partnership' => 'Kerja Sama & Kemitraan',
            'privacy' => 'Privasi & Keamanan',
        ];

        $_SESSION['contact_old_input'] = [
            'name' => $name,
            'email' => $email,
            'category' => $category,
            'message' => $message,
        ];

        if (
            $name === '' ||
            strlen($name) > 100 ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            strlen($email) > 254 ||
            !isset($categories[$category]) ||
            $message === '' ||
            strlen($message) > 5000
        ) {
            $_SESSION['contact_error'] = 'Periksa kembali formulir. Pastikan semua kolom terisi dengan benar.';
            $this->redirect('/contact');
            return;
        }

        try {
            ContactMessage::create($name, $email, $category, $message);
        } catch (PDOException $exception) {
            error_log('Contact form database insert failed: ' . $exception->getMessage());
            $_SESSION['contact_error'] = 'Pesan belum berhasil disimpan. Silakan coba lagi nanti.';
            $this->redirect('/contact');
            return;
        }

        unset($_SESSION['contact_old_input']);
        $_SESSION['contact_success'] = 'Pesan berhasil dikirim. Terima kasih sudah menghubungi kami!';
        $this->redirect('/contact');
    }
}
