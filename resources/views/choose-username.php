<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title ?? 'Pilih Username — Pseudonym') ?></title>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="icon" type="image/png" href="<?= asset('images/Pseudonym%20logo%20browser.png') ?>">
</head>
<body>
<div class="auth-page" style="position:relative; overflow:hidden; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; background:var(--dark-bg);">
    <div class="page-doodles" aria-hidden="true">
        <span class="page-doodle page-doodle--star">✳</span>
        <span class="page-doodle page-doodle--wave">〰</span>
        <span class="page-doodle page-doodle--plus">+</span>
        <span class="page-doodle page-doodle--ring"></span>
        <span class="page-doodle page-doodle--spark">✦</span>
    </div>
    <div class="paper-card auth-card" style="position:relative; z-index:1; max-width:480px; width:100%; background:var(--paper); border-radius:20px; padding:32px 28px; box-shadow:0 12px 36px rgba(0,0,0,0.3); border:1px solid var(--paper-line);">
        <h1 class="serif-heading" style="font-size:1.8rem; margin-bottom:8px;">Pilih alamat kotak suratmu</h1>
        <p class="muted" style="margin-bottom:24px; font-size:0.9rem; color:var(--text-muted-dark);">Ini akan jadi tautan unik yang kamu bagikan agar teman-teman bisa mengirim pesan rahasia kepadamu.</p>

        <?php if (!empty($error)): ?>
            <div class="alert-error" style="background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); color:#B91C1C; padding:10px 14px; border-radius:10px; font-size:0.85rem; margin-bottom:18px;">
                <?= h($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('/choose-username') ?>" class="stacked-form">
            <div style="margin-bottom:18px;">
                <label for="username" style="display:block; font-size:0.82rem; font-family:'IBM Plex Mono',monospace; color:var(--text-muted-dark); margin-bottom:6px; text-transform:uppercase;">Username</label>
                <div class="input-prefix" style="display:flex; align-items:center; background:var(--paper-2); border:1.5px solid var(--paper-line); border-radius:10px; padding:0 12px;">
                    <span style="font-family:'IBM Plex Mono',monospace; color:var(--text-muted); font-size:0.9rem;">@</span>
                    <input type="text" id="username" name="username" placeholder="contoh_nama123" required pattern="[a-zA-Z0-9_]{3,20}" style="flex:1; background:transparent; border:none; outline:none; padding:12px 8px; font-size:0.95rem; color:var(--text-dark);" autofocus>
                </div>
                <div style="font-size:0.75rem; color:var(--text-muted); margin-top:6px; font-family:'IBM Plex Mono',monospace;">* 3-20 karakter, huruf, angka, dan underscore (_).</div>
            </div>

            <button type="submit" class="btn-seal" style="width:100%; padding:14px; border-radius:12px; font-weight:700; background:var(--primary); color:var(--dark-bg); border:none; cursor:pointer; font-size:0.95rem;">
                Kunci Username Ini &rarr;
            </button>
        </form>
    </div>
</div>
</body>
</html>
