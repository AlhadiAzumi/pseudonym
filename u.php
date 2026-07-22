<?php
require_once __DIR__ . '/includes/functions.php';

$username = trim($_GET['u'] ?? '');
$viewer = currentUser();

if ($username === '') {
    header('Location: ' . APP_URL . '/index.php');
    exit;
}

$stmt = getDB()->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute([$username]);
$owner = $stmt->fetch();

if (!$owner) {
    http_response_code(404);
    ?>
    <!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">
    <title>Tidak ditemukan — Pseudonym</title>
    <link rel="stylesheet" href="public/assets/css/style.css"></head>
    <body>
    <div class="main-curved-box" style="min-height:100vh; display:flex; align-items:center; justify-content:center;">
        <div class="empty-state" style="text-align:center; padding:60px 20px; color:var(--muted);">
            Kotak surat "@<?= h($username) ?>" tidak ditemukan.<br><br>
            <a href="index.php" class="btn-outline" style="border:1.5px solid var(--primary); background:transparent; color:var(--primary); padding:12px 22px; border-radius:10px; font-weight:600; text-decoration:none; display:inline-block;">← Kembali</a>
        </div>
    </div>
    </body></html>
    <?php
    exit;
}

$isOwner = $viewer && $viewer['id'] === $owner['id'];

// Ambil semua pesan + balasan
$stmt = getDB()->prepare(
    'SELECT m.id, m.content, m.created_at, m.is_hidden,
            r.content AS reply_content, r.created_at AS reply_created_at
     FROM messages m
     LEFT JOIN replies r ON r.message_id = m.id
     WHERE m.receiver_id = ?' . ($isOwner ? '' : ' AND m.is_hidden = 0') . '
     ORDER BY m.created_at DESC'
);
$stmt->execute([$owner['id']]);
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@<?= h($owner['username']) ?> — Pseudonym</title>
<link rel="stylesheet" href="public/assets/css/style.css">
<link rel="icon" type="image/png" href="public/assets/images/Pseudonym%20logo%20browser.png">
</head>
<body>

<!-- MARQUEE -->
<div class="marquee-container" id="marquee">
    <div class="marquee-content">
        <span>✨ Pseudonym — Kirim pesan rahasia tanpa identitas</span>
        <span>✨ Pseudonym — Kirim pesan rahasia tanpa identitas</span>
        <span>✨ Pseudonym — Kirim pesan rahasia tanpa identitas</span>
        <span>✨ Pseudonym — Kirim pesan rahasia tanpa identitas</span>
    </div>
</div>

<!-- MAIN CURVED BOX -->
<div class="main-curved-box">

<!-- TOPBAR -->
<div class="topbar" id="navbar">
    <div class="brand">
        <a href="index.php">
            <img src="public/assets/images/Pseudonym logo.png" alt="Pseudonym" class="brand-logo">
        </a>
    </div>
    
    <!-- NAV CENTER (Desktop) -->
    <div class="nav-center">
        <a href="index.php" class="nav-text-link">Beranda</a>
        <?php if ($isOwner): ?>
            <a href="dashboard.php" class="nav-text-link active">Kotak Masuk</a>
            <a href="auth/logout.php" class="nav-text-link" style="color: var(--primary);">Keluar</a>
        <?php else: ?>
            <a href="auth/login.php" class="nav-text-link">Masuk</a>
        <?php endif; ?>
    </div>
    
    <!-- HAMBURGER BUTTON -->
    <button class="hamburger-btn" id="hamburgerBtn" aria-label="Menu">
        <span></span>
        <span></span>
        <span></span>
    </button>
    
    <!-- MOBILE MENU -->
    <div class="mobile-menu" id="mobileMenu">
        <a href="index.php" class="mobile-menu-link">Beranda</a>
        <?php if ($isOwner): ?>
            <a href="dashboard.php" class="mobile-menu-link active">Kotak Masuk</a>
            <a href="auth/logout.php" class="mobile-menu-link mobile-menu-cta">Keluar</a>
        <?php else: ?>
            <a href="auth/login.php" class="mobile-menu-link mobile-menu-cta">Masuk</a>
        <?php endif; ?>
    </div>
</div>

<div class="page-wrap" style="padding-top:80px; padding-bottom:40px;">
    
    <!-- PROFIL HEADER - HANYA TAMPILKAN USERNAME CUSTOM -->
    <div class="profile-header" style="display:flex; align-items:center; gap:16px; margin-bottom:28px; max-width:1100px; margin-left:auto; margin-right:auto; padding:0 16px;">
        <?php if ($owner['avatar_url']): ?>
            <img src="<?= h($owner['avatar_url']) ?>" class="avatar" alt="" style="width:64px; height:64px; border-radius:50%; object-fit:cover; border:2px solid var(--primary);">
        <?php else: ?>
            <div class="avatar-fallback" style="width:64px; height:64px; border-radius:50%; background:var(--dark-bg-2); border:2px solid var(--primary); display:flex; align-items:center; justify-content:center; font-size:1.5rem; color:var(--primary); font-weight:700;"><?= h(strtoupper($owner['username'][0])) ?></div>
        <?php endif; ?>
        <div>
            <!-- HANYA TAMPILKAN USERNAME, BUKAN DISPLAY NAME DARI GOOGLE -->
            <h1 class="serif-heading" style="font-size:1.5rem; margin:0;"><?= h($owner['username']) ?></h1>
            <div class="handle" style="color:var(--muted); font-family:'IBM Plex Mono',monospace; font-size:0.85rem;">@<?= h($owner['username']) ?></div>
        </div>
    </div>

    <!-- GRID: KIRI (Pesan) + KANAN (Form Kirim) -->
    <div class="hero profile-grid" style="display:grid; grid-template-columns:1fr 340px; gap:40px; max-width:1100px; margin:0 auto; padding:0 16px; align-items:start;">
        
        <!-- KOLOM KIRI: DAFTAR PESAN -->
        <div>
            <div class="section-label" style="font-family:'IBM Plex Mono',monospace; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:var(--text-muted-dark); margin:0 0 20px; display:flex; align-items:center; gap:10px;">
                <span>📬 Semua Pesan (<?= count($messages) ?>)</span>
                <span style="flex:1; height:1px; background:rgba(17,24,39,0.08);"></span>
            </div>

            <div class="message-list" id="message-list" data-is-owner="<?= $isOwner ? '1' : '0' ?>" data-username="<?= h($owner['username']) ?>" style="margin-bottom:40px;">
                <?php if (empty($messages)): ?>
                    <div class="empty-state" id="empty-state" style="text-align:center; padding:60px 20px; color:var(--muted); border:1px dashed var(--paper-line); border-radius:16px; background:var(--paper-2);">
                        <div style="font-size:3rem; margin-bottom:12px;">📭</div>
                        Belum ada pesan. Jadilah yang pertama mengirim.
                    </div>
                <?php endif; ?>

                <?php foreach ($messages as $msg): ?>
                    <?php $hasReply = $msg['reply_content'] !== null; ?>
<div class="message-card" data-message-id="<?= (int)$msg['id'] ?>" style="background:var(--paper); ...">                        <div class="message-meta" style="display:flex; justify-content:flex-end; align-items:center; margin-bottom:10px;">
                            <span class="message-time" style="font-family:'IBM Plex Mono',monospace; font-size:0.72rem; color:var(--text-muted-dark);"><?= h(timeAgo($msg['created_at'])) ?></span>
                        </div>
                        <div class="message-content" style="font-size:0.98rem; color:var(--text-dark); white-space:pre-wrap; word-break:break-word;"><?= h($msg['content']) ?></div>

                        <?php if ($hasReply): ?>
                            <div class="reply-block" style="margin-top:14px; padding:14px 16px; background:rgba(201,162,39,0.06); border-left:2px solid var(--primary); border-radius:0 8px 8px 0;">
                                <div class="reply-content" style="font-size:0.92rem; white-space:pre-wrap; word-break:break-word;"><?= h($msg['reply_content']) ?></div>
                            </div>

                            <?php if ($isOwner): ?>
                            <div class="social-share-buttons" style="display:flex; gap:8px; margin-top:12px; flex-wrap:wrap;">
                                <button class="share-btn copy-link" data-action="share" data-type="copy" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; font-size:0.75rem; padding:8px 12px; border-radius:8px; border:none; cursor:pointer; font-weight:600; background:var(--primary); color:var(--dark-bg); transition:all .2s;">📋 Copy Link</button>
                                <a class="share-btn twitter" href="javascript:void(0)" data-action="share" data-type="twitter" target="_blank" rel="noopener" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; font-size:0.75rem; padding:8px 12px; border-radius:8px; border:none; cursor:pointer; font-weight:600; background:rgba(29,161,242,0.12); color:#1DA1F2; border:1px solid rgba(29,161,242,0.3); text-decoration:none; transition:all .2s;">𝕏 Share</a>
                                <a class="share-btn whatsapp" href="javascript:void(0)" data-action="share" data-type="whatsapp" target="_blank" rel="noopener" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; font-size:0.75rem; padding:8px 12px; border-radius:8px; border:none; cursor:pointer; font-weight:600; background:rgba(37,211,102,0.12); color:#25D366; border:1px solid rgba(37,211,102,0.3); text-decoration:none; transition:all .2s;">💬 Share</a>
                                <a class="share-btn facebook" href="javascript:void(0)" data-action="share" data-type="facebook" target="_blank" rel="noopener" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; font-size:0.75rem; padding:8px 12px; border-radius:8px; border:none; cursor:pointer; font-weight:600; background:rgba(59,89,152,0.12); color:#3B5998; border:1px solid rgba(59,89,152,0.3); text-decoration:none; transition:all .2s;">f Share</a>
                            </div>
                            <?php endif; ?>
                        <?php elseif ($isOwner): ?>
                            <button class="reply-toggle" data-action="toggle-reply" style="background:none; border:none; color:var(--primary); font-size:0.82rem; cursor:pointer; padding:4px 0; margin-top:10px; font-weight:500;">+ Balas pesan ini</button>
                            <div class="reply-form" style="margin-top:14px; display:none;">
                                <textarea placeholder="Tulis balasanmu…" maxlength="<?= (int)MAX_REPLY_LENGTH ?>" style="width:100%; background:var(--paper-2); border:1.5px solid var(--paper-line); border-radius:8px; padding:10px 12px; color:var(--text-dark); font-size:0.9rem; resize:vertical; min-height:60px; outline:none; font-family:'Inter',sans-serif;"></textarea>
                                <div class="reply-actions" style="display:flex; gap:10px; margin-top:12px;">
                                    <button class="btn-small" data-action="submit-reply" style="font-size:0.82rem; padding:8px 14px; border-radius:8px; border:none; cursor:pointer; background:var(--primary); color:var(--dark-bg); font-weight:600; transition:background .2s;">Kirim Balasan</button>
                                    <button class="btn-small ghost" data-action="cancel-reply" style="font-size:0.82rem; padding:8px 14px; border-radius:8px; border:1.5px solid var(--paper-line); background:transparent; color:var(--text-muted); cursor:pointer; font-weight:600; transition:all .2s;">Batal</button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- KOLOM KANAN: FORM KIRIM PESAN - DIPERBAIKI LAYOUTNYA -->
        <aside class="compose-col" style="position:sticky; top:100px; align-self:start;">
            <div class="envelope compose-box" style="background:rgba(255,255,255,0.15); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.2); border-radius:24px; padding:28px; box-shadow:0 8px 32px rgba(0,0,0,0.06), inset 0 1px 0 rgba(255,255,255,0.3);">
                <textarea id="message-input" maxlength="<?= (int)MAX_MESSAGE_LENGTH ?>" placeholder="Tulis apa yang ingin kamu sampaikan…" style="width:100%; background:rgba(255,255,255,0.3); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,0.2); border-radius:16px; padding:16px 18px; font-size:0.98rem; color:var(--text-dark); resize:vertical; min-height:120px; outline:none; transition:all 0.3s ease; font-family:'Inter',sans-serif;"></textarea>
                <div class="compose-footer" style="display:flex; justify-content:space-between; align-items:center; margin-top:14px;">
                    <span class="char-count" style="font-size:0.78rem; color:var(--text-muted); font-family:'IBM Plex Mono',monospace; background:rgba(255,255,255,0.1); padding:4px 12px; border-radius:999px; border:1px solid rgba(255,255,255,0.1);">
                        <span id="char-count">0</span>/<?= (int)MAX_MESSAGE_LENGTH ?>
                    </span>
                    <button class="btn-seal" id="send-btn" style="background:var(--primary); color:var(--dark-bg); border:none; border-radius:14px; padding:12px 32px; font-weight:700; font-size:0.95rem; cursor:pointer; transition:all 0.3s ease; box-shadow:0 4px 20px rgba(204,255,0,0.25);">
                        Kirim Diam-diam
                    </button>
                </div>
            </div>
        </aside>

    </div>
</div>

<!-- FOOTER DARK -->
<div class="site-footer-dark" style="padding:32px 24px 40px; color:#6B7280; font-size:0.9rem; border-top:1px solid rgba(255,255,255,0.06); margin-top:20px;">
    <div class="footer-grid" style="display:flex; justify-content:space-between; gap:20px; flex-wrap:wrap; max-width:1100px; margin:0 auto;">
        <div>&copy; <?= date('Y') ?> Pseudonym</div>
        <div><a href="about.php" style="color:#9CA3AF; text-decoration:none;">Tentang</a></div>
        <div><a href="#" style="color:#9CA3AF; text-decoration:none;">Privasi</a></div>
        <div><a href="#" style="color:#9CA3AF; text-decoration:none;">Bantuan</a></div>
    </div>
</div>

</div><!-- end main-curved-box -->

<script src="public/assets/js/profile.js"></script>
<script>
// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('navbar');
    const marquee = document.getElementById('marquee');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
        marquee.classList.add('hide');
    } else {
        navbar.classList.remove('scrolled');
        marquee.classList.remove('hide');
    }
});

// Hamburger menu
const hamburgerBtn = document.getElementById('hamburgerBtn');
const mobileMenu = document.getElementById('mobileMenu');

hamburgerBtn.addEventListener('click', function() {
    this.classList.toggle('open');
    mobileMenu.classList.toggle('open');
});

document.querySelectorAll('.mobile-menu-link').forEach(link => {
    link.addEventListener('click', () => {
        hamburgerBtn.classList.remove('open');
        mobileMenu.classList.remove('open');
    });
});

// Character counter
const msgInput = document.getElementById('message-input');
const charCount = document.getElementById('char-count');
if (msgInput && charCount) {
    msgInput.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });
}
</script>

</body>
</html>