<?php
$currentUser = currentUser();
$currentPage = $currentPage ?? '';
?>
<div class="marquee-container" id="marquee">
    <div class="marquee-content">
        <span>KIRIM PESAN RAHASIA • TANPA NAMA PENGIRIM • AMAN & ANONIM • UNGKAPKAN PERASAANMU • </span>
        <span>KIRIM PESAN RAHASIA • TANPA NAMA PENGIRIM • AMAN & ANONIM • UNGKAPKAN PERASAANMU • </span>
        <span>KIRIM PESAN RAHASIA • TANPA NAMA PENGIRIM • AMAN & ANONIM • UNGKAPKAN PERASAANMU • </span>
        <span>KIRIM PESAN RAHASIA • TANPA NAMA PENGIRIM • AMAN & ANONIM • UNGKAPKAN PERASAANMU • </span>
    </div>
</div>

<div class="main-curved-box">
    <!-- Navbar -->
    <div class="topbar" id="navbar">
        <div class="brand" style="flex:1;">
            <a href="<?= url('/') ?>">
                <img src="<?= asset('images/Pseudonym%20logo.png') ?>" alt="Pseudonym" class="brand-logo">
            </a>
        </div>

        <div class="nav-center">
            <a href="<?= url('/about') ?>" class="nav-text-link <?= $currentPage === 'about' ? 'active' : '' ?>">About</a>
            <a href="<?= url('/safety') ?>" class="nav-text-link <?= $currentPage === 'safety' ? 'active' : '' ?>">Safety</a>
            <a href="<?= url('/contact') ?>" class="nav-text-link <?= $currentPage === 'contact' ? 'active' : '' ?>">Contact us</a>
        </div>

        <div class="nav-right" style="flex:1; display:flex; justify-content:flex-end; align-items:center; gap:12px;">
            <?php if ($currentUser): ?>
                <a href="<?= url('/dashboard') ?>" class="nav-link glass-btn desktop-only-link">Kotak Masuk</a>
                <a href="<?= url('/auth/logout') ?>" class="nav-text-link nav-logout desktop-only-link">Keluar</a>
            <?php else: ?>
                <a href="<?= url('/auth/google-login') ?>" class="nav-link glass-btn desktop-only-link">Masuk</a>
            <?php endif; ?>

            <button class="hamburger-btn" id="hamburger-btn" aria-label="Buka menu" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        <div class="mobile-menu" id="mobile-menu">
            <a href="<?= url('/') ?>" class="mobile-menu-link <?= $currentPage === 'home' ? 'active' : '' ?>">Beranda</a>
            <a href="<?= url('/about') ?>" class="mobile-menu-link <?= $currentPage === 'about' ? 'active' : '' ?>">About</a>
            <a href="<?= url('/safety') ?>" class="mobile-menu-link <?= $currentPage === 'safety' ? 'active' : '' ?>">Safety</a>
            <a href="<?= url('/contact') ?>" class="mobile-menu-link <?= $currentPage === 'contact' ? 'active' : '' ?>">Contact us</a>
            <?php if ($currentUser): ?>
                <a href="<?= url('/dashboard') ?>" class="mobile-menu-link active">Kotak Masuk</a>
                <a href="<?= url('/auth/logout') ?>" class="mobile-menu-link mobile-menu-cta" style="color:#ef4444;">Keluar</a>
            <?php else: ?>
                <a href="<?= url('/auth/google-login') ?>" class="mobile-menu-link mobile-menu-cta">Masuk dengan Google</a>
            <?php endif; ?>
        </div>
    </div>