<div class="marquee-container" id="marquee">
    <div class="marquee-content">
        <span>✨ Pseudonym — Kirim pesan rahasia tanpa identitas</span>
        <span>✨ Pseudonym — Kirim pesan rahasia tanpa identitas</span>
        <span>✨ Pseudonym — Kirim pesan rahasia tanpa identitas</span>
        <span>✨ Pseudonym — Kirim pesan rahasia tanpa identitas</span>
    </div>
</div>

<div class="main-curved-box">
    <div class="topbar" id="navbar">
        <div class="brand">
            <a href="<?= url('/') ?>">
                <img src="<?= asset('images/Pseudonym%20logo.png') ?>" alt="Pseudonym" class="brand-logo">
            </a>
        </div>

        <div class="nav-center">
            <a href="<?= url('/') ?>" class="nav-text-link">Beranda</a>
            <a href="<?= url('/dashboard') ?>" class="nav-text-link active">Kotak Masuk</a>
            <a href="<?= url('/auth/logout') ?>" class="nav-text-link nav-logout">Keluar</a>
        </div>

        <button class="hamburger-btn" id="hamburger-btn" aria-label="Menu" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="mobile-menu" id="mobile-menu">
            <a href="<?= url('/') ?>" class="mobile-menu-link">Beranda</a>
            <a href="<?= url('/dashboard') ?>" class="mobile-menu-link active">Kotak Masuk</a>
            <a href="<?= url('/auth/logout') ?>" class="mobile-menu-link mobile-menu-cta">Keluar</a>
        </div>
    </div>