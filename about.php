<?php
require_once __DIR__ . '/includes/functions.php';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About - Pseudonym</title>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="icon" type="image/png" href="public/assets/images/Pseudonym%20logo%20browser.png">
</head>
<body>
<!-- MARQUEE (Teks Berjalan di Paling Atas) -->
    <div class="marquee-container">
        <div class="marquee-content">
            <span>KIRIM PESAN RAHASIA • TANPA NAMA PENGIRIM • AMAN & ANONIM • UNGKAPKAN PERASAANMU • </span>
            <span>KIRIM PESAN RAHASIA • TANPA NAMA PENGIRIM • AMAN & ANONIM • UNGKAPKAN PERASAANMU • </span>
            <span>KIRIM PESAN RAHASIA • TANPA NAMA PENGIRIM • AMAN & ANONIM • UNGKAPKAN PERASAANMU • </span>
            <span>KIRIM PESAN RAHASIA • TANPA NAMA PENGIRIM • AMAN & ANONIM • UNGKAPKAN PERASAANMU • </span>
        </div>
    </div>

    <!-- KONTAINER MELENGKUNG -->
    <div class="main-curved-box">
        
        <div class="topbar" id="navbar">
          <div class="brand" style="flex: 1;">
            <a href="index.php">
              <img src="public/assets/images/Pseudonym logo.png" alt="Pseudonym" class="brand-logo">
            </a>
          </div>

          <!-- Tengah: Menu Link (About aktif, ada titik di bawahnya) -->
          <div class="nav-center">
            <a href="about.php" class="nav-text-link active">About</a>
            <a href="index.php#" class="nav-text-link">Safety</a>
            <a href="index.php#" class="nav-text-link">Contact us</a>
          </div>

          <div class="nav-right" style="flex: 1; display: flex; justify-content: flex-end; align-items: center; gap: 12px;">
              <?php if (isset($isOwner) && $isOwner): ?>
                <a href="dashboard.php" class="nav-link glass-btn desktop-only-link">Kotak Masuk</a>
              <?php elseif (isset($viewer) && $viewer): ?>
                <a href="dashboard.php" class="nav-link glass-btn desktop-only-link">Kotak Masukmu</a>
              <?php else: ?>
                <a href="auth/google-login.php" class="nav-link glass-btn desktop-only-link">Masuk</a>
              <?php endif; ?>

              <button class="hamburger-btn" id="hamburger-btn" aria-label="Buka menu" aria-expanded="false">
                <span></span><span></span><span></span>
              </button>
          </div>

          <!-- Dropdown Menu Mobile (ala NGL) -->
          <div class="mobile-menu" id="mobile-menu">
            <a href="about.php" class="mobile-menu-link active">About</a>
            <a href="index.php#" class="mobile-menu-link">Safety</a>
            <a href="index.php#" class="mobile-menu-link">Contact us</a>
            <?php if (isset($isOwner) && $isOwner): ?>
              <a href="dashboard.php" class="mobile-menu-link mobile-menu-cta">Kotak Masuk</a>
            <?php elseif (isset($viewer) && $viewer): ?>
              <a href="dashboard.php" class="mobile-menu-link mobile-menu-cta">Kotak Masukmu</a>
            <?php else: ?>
              <a href="auth/google-login.php" class="mobile-menu-link mobile-menu-cta">Masuk</a>
            <?php endif; ?>
          </div>
        </div>

        <div class="page-wrap">
            
          <!-- Latest Stories -->
          <div class="latest-stories" style="margin-top: 0;">
            <div class="story-main">
              <h2 class="serif-heading">Latest Stories</h2>
              <p class="muted">Kumpulan cerita terbaru dari pengguna dan tim kami.</p>
              <div class="story-details">
                  <p class="story-meta">Kisah inspiratif, pesan anonim, dan pembaruan kecil yang dibuat untuk pembaca setia.</p>
              </div>
            </div>
            <div class="story-list">
              <div class="story-item">
                <div class="thumb"></div>
                <div>
                  <div class="story-title">Tips Menulis Pesan Efektif</div>
                  <div class="story-meta">Ringkasan singkat tentang cara menggunakan platform</div>
                </div>
              </div>
              <div class="story-item">
                <div class="thumb"></div>
                <div>
                  <div class="story-title">Cerita dari Pengguna</div>
                  <div class="story-meta">Bagaimana orang memanfaatkan pesan anonim</div>
                </div>
              </div>
            </div>
          </div>

          <!-- About & Features -->
          <div class="highlights">
            <div class="highlight-card">
              <div class="highlight-title">Anonim Sepenuhnya</div>
              <div class="highlight-desc">Identitas pengirim tidak pernah disimpan atau diketahui.</div>
            </div>
            <div class="highlight-card">
              <div class="highlight-title">Mudah Digunakan</div>
              <div class="highlight-desc">Cukup ketik username dan kirim pesan. Tidak perlu daftar.</div>
            </div>
            <div class="highlight-card">
              <div class="highlight-title">Kontrol Penuh</div>
              <div class="highlight-desc">Pemilik kotak bisa balas, sembunyikan, atau hapus pesan.</div>
            </div>
          </div>

        </div>
    </div> <!-- Penutup main-curved-box -->

    <div class="site-footer-dark">
      <div class="footer-grid">
        <div>© Pseudonym</div>
        <div><a href="about.php">About</a> • Help • Privacy</div>
      </div>
    </div>

    <script>
      // Script Navbar
      window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        const marquee = document.querySelector('.marquee-container');
        if (window.scrollY > 50) {
          navbar.classList.add('scrolled');
          if (marquee) marquee.classList.add('hide');
        } else {
          navbar.classList.remove('scrolled');
          if (marquee) marquee.classList.remove('hide');
        }
      });

      // Script Hamburger Menu (Mobile)
      const hamburgerBtn = document.getElementById('hamburger-btn');
      const mobileMenu = document.getElementById('mobile-menu');
      if (hamburgerBtn && mobileMenu) {
        hamburgerBtn.addEventListener('click', function() {
          const isOpen = mobileMenu.classList.toggle('open');
          hamburgerBtn.classList.toggle('open', isOpen);
          hamburgerBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
        mobileMenu.querySelectorAll('a').forEach(function(link) {
          link.addEventListener('click', function() {
            mobileMenu.classList.remove('open');
            hamburgerBtn.classList.remove('open');
            hamburgerBtn.setAttribute('aria-expanded', 'false');
          });
        });
        window.addEventListener('resize', function() {
          if (window.innerWidth > 780) {
            mobileMenu.classList.remove('open');
            hamburgerBtn.classList.remove('open');
            hamburgerBtn.setAttribute('aria-expanded', 'false');
          }
        });
      }
    </script>
</body>
</html>