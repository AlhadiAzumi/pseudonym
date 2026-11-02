<?php
$reportReasons = [
    'harassment' => 'Pelecehan',
    'hate_speech' => 'Ujaran kebencian',
    'personal_info' => 'Informasi pribadi',
    'threat' => 'Ancaman',
    'spam' => 'Spam',
    'other' => 'Lainnya',
];
$contactCategories = [
    'general' => 'Pertanyaan umum',
    'bug' => 'Laporan masalah',
    'partnership' => 'Kerja sama',
    'privacy' => 'Privasi & keamanan',
];
?>
<main class="page-wrap admin-page">
    <header class="admin-heading">
        <p class="admin-eyebrow">Area terbatas · <?= h($admin['email'] ?? '') ?></p>
        <h1 class="serif-heading">Administrasi</h1>
        <p class="admin-intro">Tinjau laporan komunitas dan baca pesan yang masuk lewat Contact Us.</p>
    </header>

    <?php if (!empty($flash)): ?>
        <p class="admin-flash admin-flash--<?= h($flash['type'] ?? 'success') ?>" role="<?= ($flash['type'] ?? '') === 'error' ? 'alert' : 'status' ?>">
            <?= h($flash['message'] ?? '') ?>
        </p>
    <?php endif; ?>

    <nav class="admin-tabs" aria-label="Bagian administrasi">
        <a href="#reports">Laporan <span><?= (int) $reportsTotal ?></span></a>
        <a href="#contacts">Contact Us <span><?= (int) $contactsTotal ?></span></a>
    </nav>

    <section class="admin-section" id="reports" aria-labelledby="reports-title">
        <div class="admin-section-heading">
            <div>
                <p class="admin-eyebrow">Moderasi</p>
                <h2 id="reports-title">Laporan pesan</h2>
            </div>
            <span class="admin-count"><?= (int) $reportsTotal ?> total</span>
        </div>

        <?php if (empty($reports)): ?>
            <div class="admin-empty">Belum ada laporan pesan.</div>
        <?php else: ?>
            <div class="admin-list">
                <?php foreach ($reports as $report): ?>
                    <?php $isHidden = in_array($report['is_hidden'], [true, 1, '1', 't', 'true'], true); ?>
                    <article class="admin-card">
                        <div class="admin-card-top">
                            <div class="admin-badges">
                                <span class="admin-badge admin-badge--<?= h($report['status']) ?>"><?= h(ucfirst($report['status'])) ?></span>
                                <span class="admin-badge"><?= h($reportReasons[$report['reason']] ?? $report['reason']) ?></span>
                                <span class="admin-badge">Pesan #<?= (int) $report['message_id'] ?></span>
                            </div>
                            <time datetime="<?= h((string) $report['created_at']) ?>"><?= h(date('d M Y, H:i', strtotime((string) $report['created_at']))) ?></time>
                        </div>

                        <blockquote class="admin-message"><?= nl2br(h((string) $report['message_content'])) ?></blockquote>
                        <p class="admin-card-meta">
                            Dilaporkan pada profil
                            <a href="<?= h(url('/u/' . rawurlencode((string) $report['receiver_username']))) ?>" target="_blank" rel="noopener noreferrer">
                                @<?= h((string) $report['receiver_username']) ?>
                            </a>
                            <?= $isHidden ? '· pesan sudah disembunyikan' : '' ?>
                        </p>

                        <div class="admin-actions">
                            <?php if ($report['status'] === 'pending'): ?>
                                <?php foreach (['reviewed' => 'Tandai ditinjau', 'dismissed' => 'Dismiss'] as $action => $label): ?>
                                    <form method="POST" action="<?= url('/admin/reports') ?>">
                                        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                                        <input type="hidden" name="report_id" value="<?= (int) $report['id'] ?>">
                                        <input type="hidden" name="action" value="<?= h($action) ?>">
                                        <button class="admin-button" type="submit"><?= h($label) ?></button>
                                    </form>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if (!$isHidden): ?>
                                <form method="POST" action="<?= url('/admin/reports') ?>">
                                    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                                    <input type="hidden" name="report_id" value="<?= (int) $report['id'] ?>">
                                    <input type="hidden" name="action" value="hide">
                                    <button class="admin-button admin-button--danger" type="submit">Sembunyikan pesan</button>
                                </form>
                            <?php endif; ?>

                            <form method="POST" action="<?= url('/admin/reports') ?>" onsubmit="return confirm('Hapus entri laporan ini? Pesan aslinya tidak akan dihapus.');">
                                <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                                <input type="hidden" name="report_id" value="<?= (int) $report['id'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <button class="admin-button admin-button--quiet" type="submit">Hapus laporan</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if ($reportsPages > 1): ?>
                <nav class="admin-pagination" aria-label="Halaman laporan">
                    <?php if ($reportsPage > 1): ?><a href="<?= url('/admin?reports_page=' . ($reportsPage - 1) . '#reports') ?>">← Lebih baru</a><?php endif; ?>
                    <span>Halaman <?= (int) $reportsPage ?> dari <?= (int) $reportsPages ?></span>
                    <?php if ($reportsPage < $reportsPages): ?><a href="<?= url('/admin?reports_page=' . ($reportsPage + 1) . '#reports') ?>">Lebih lama →</a><?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </section>

    <section class="admin-section" id="contacts" aria-labelledby="contacts-title">
        <div class="admin-section-heading">
            <div>
                <p class="admin-eyebrow">Pesan masuk</p>
                <h2 id="contacts-title">Contact Us</h2>
            </div>
            <span class="admin-count"><?= (int) $contactsTotal ?> total</span>
        </div>

        <?php if (empty($contacts)): ?>
            <div class="admin-empty">Belum ada pesan Contact Us.</div>
        <?php else: ?>
            <div class="admin-list">
                <?php foreach ($contacts as $contact): ?>
                    <article class="admin-card">
                        <div class="admin-card-top">
                            <div class="admin-badges">
                                <span class="admin-badge"><?= h($contactCategories[$contact['category']] ?? $contact['category']) ?></span>
                            </div>
                            <time datetime="<?= h((string) $contact['created_at']) ?>"><?= h(date('d M Y, H:i', strtotime((string) $contact['created_at']))) ?></time>
                        </div>
                        <h3 class="admin-contact-name"><?= h((string) $contact['name']) ?></h3>
                        <a class="admin-contact-email" href="mailto:<?= h((string) $contact['email']) ?>"><?= h((string) $contact['email']) ?></a>
                        <p class="admin-contact-message"><?= nl2br(h((string) $contact['message'])) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if ($contactsPages > 1): ?>
                <nav class="admin-pagination" aria-label="Halaman pesan kontak">
                    <?php if ($contactsPage > 1): ?><a href="<?= url('/admin?contacts_page=' . ($contactsPage - 1) . '#contacts') ?>">← Lebih baru</a><?php endif; ?>
                    <span>Halaman <?= (int) $contactsPage ?> dari <?= (int) $contactsPages ?></span>
                    <?php if ($contactsPage < $contactsPages): ?><a href="<?= url('/admin?contacts_page=' . ($contactsPage + 1) . '#contacts') ?>">Lebih lama →</a><?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>
