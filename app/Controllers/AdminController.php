<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ContactMessage;
use App\Models\MessageReport;
use Throwable;

class AdminController extends Controller
{
    private const PAGE_SIZE = 25;

    public function index(): void
    {
        $admin = $this->requireAdmin();
        $reportsPage = max(1, (int) ($_GET['reports_page'] ?? 1));
        $contactsPage = max(1, (int) ($_GET['contacts_page'] ?? 1));
        $reportsTotal = MessageReport::countAll();
        $contactsTotal = ContactMessage::countAll();
        $reportsPages = max(1, (int) ceil($reportsTotal / self::PAGE_SIZE));
        $contactsPages = max(1, (int) ceil($contactsTotal / self::PAGE_SIZE));
        $reportsPage = min($reportsPage, $reportsPages);
        $contactsPage = min($contactsPage, $contactsPages);
        $flash = $_SESSION['admin_flash'] ?? null;
        unset($_SESSION['admin_flash']);

        $this->view('admin', [
            'title' => 'Administrasi',
            'currentPage' => 'admin',
            'admin' => $admin,
            'flash' => is_array($flash) ? $flash : null,
            'reports' => MessageReport::getRecent(
                self::PAGE_SIZE,
                ($reportsPage - 1) * self::PAGE_SIZE
            ),
            'reportsPage' => $reportsPage,
            'reportsPages' => $reportsPages,
            'reportsTotal' => $reportsTotal,
            'contacts' => ContactMessage::getRecent(
                self::PAGE_SIZE,
                ($contactsPage - 1) * self::PAGE_SIZE
            ),
            'contactsPage' => $contactsPage,
            'contactsPages' => $contactsPages,
            'contactsTotal' => $contactsTotal,
        ]);
    }

    public function updateReport(): void
    {
        $this->requireAdmin();

        $reportId = filter_var($_POST['report_id'] ?? null, FILTER_VALIDATE_INT);
        $action = $_POST['action'] ?? '';
        if ($reportId === false || $reportId === null || $reportId < 1 || !is_string($action)) {
            $_SESSION['admin_flash'] = ['type' => 'error', 'message' => 'Data laporan tidak valid.'];
            $this->redirect('/admin?section=reports');
        }

        try {
            $success = match ($action) {
                'reviewed', 'dismissed' => MessageReport::updateStatus($reportId, $action),
                'hide' => MessageReport::hideReportedMessage($reportId),
                'delete' => MessageReport::deleteById($reportId),
                default => false,
            };

            $_SESSION['admin_flash'] = $success
                ? ['type' => 'success', 'message' => 'Perubahan laporan berhasil disimpan.']
                : ['type' => 'error', 'message' => 'Laporan tidak ditemukan atau tindakan tidak valid.'];
        } catch (Throwable $exception) {
            error_log(sprintf(
                'Admin report action failed: %s: %s in %s:%d',
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            ));
            $_SESSION['admin_flash'] = ['type' => 'error', 'message' => 'Laporan gagal diperbarui. Coba lagi nanti.'];
        }

        $this->redirect('/admin?section=reports');
    }

    private function requireAdmin(): array
    {
        $user = currentUser();
        if (!$user) {
            $this->redirect('/auth/google-login');
        }

        if (!isAdminUser($user)) {
            http_response_code(403);
            $this->view('access-denied', [
                'title' => 'Akses Ditolak',
                'metaDescription' => 'Halaman ini hanya dapat diakses oleh administrator.',
                'robots' => 'noindex, nofollow',
                'currentPage' => '',
            ]);
            exit;
        }

        return $user;
    }
}
