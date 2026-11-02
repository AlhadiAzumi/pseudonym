<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title ?? 'Pilih username') ?></title>
    <meta name="csrf-token" content="<?= h(csrfToken()) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="icon" type="image/png" href="<?= asset('images/Pseudonym%20logo%20browser.png') ?>">
</head>
<body>
<main class="auth-page username-page">
    <div class="page-doodles" aria-hidden="true">
        <span class="page-doodle page-doodle--star">✳</span>
        <span class="page-doodle page-doodle--wave">〰</span>
        <span class="page-doodle page-doodle--plus">+</span>
        <span class="page-doodle page-doodle--ring"></span>
        <span class="page-doodle page-doodle--spark">✦</span>
    </div>
    <section class="paper-card auth-card username-card" aria-labelledby="username-title">
        <p class="username-eyebrow"><span aria-hidden="true"></span> Satu langkah lagi</p>
        <h1 class="serif-heading username-title" id="username-title">Buat alamat kotak suratmu.</h1>
        <p class="muted username-description">Pilih username untuk membuat tautan pribadi yang bisa kamu bagikan agar teman-teman dapat mengirim pesan rahasia.</p>

        <?php if (!empty($error)): ?>
            <div class="alert-error username-error" role="alert">
                <?= h($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('/choose-username') ?>" class="stacked-form username-form">
            <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
            <div class="username-field">
                <label for="username">Username</label>
                <div class="input-prefix username-input">
                    <span aria-hidden="true">@</span>
                    <input type="text" id="username" name="username" placeholder="contoh_nama123" required pattern="[a-zA-Z0-9_]{3,20}" minlength="3" maxlength="20" autocomplete="username" autocapitalize="none" spellcheck="false" autofocus>
                </div>
                <p class="username-hint">3–20 karakter. Gunakan huruf, angka, atau garis bawah (_).</p>
            </div>

            <button type="submit" class="btn-seal username-submit">
                Simpan username <span aria-hidden="true">→</span>
            </button>
        </form>
    </section>
</main>
</body>
</html>
