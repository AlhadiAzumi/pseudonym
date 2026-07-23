$user = currentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pseudonym</title>
<link rel="stylesheet" href="public/assets/css/style.css">
<link rel="icon" type="image/png" href="public/assets/images/Pseudonym%20logo%20browser.png">
</head>
<body>
    <div id="intro-overlay">
        <div class="intro-container">
            <img src="public/assets/images/Pseudonym logo browser.png" alt="" class="logo-question">
            <img src="public/assets/images/Pseudonym logo.png" alt="Pseudonym" class="logo-text">
        </div>
    </div>

    <div class="marquee-container">
        <div class="marquee-content">
            <span>kirim pesan rahasia • tanpa nama pengirim • aman & anonim • ungkapkan perasaanmu • </span>
            <span>kirim pesan rahasia • tanpa nama pengirim • aman & anonim • ungkapkan perasaanmu • </span>
            <span>kirim pesan rahasia • tanpa nama pengirim • aman & anonim • ungkapkan perasaanmu • </span>
            <span>kirim pesan rahasia • tanpa nama pengirim • aman & anonim • ungkapkan perasaanmu • </span>
        </div>
    </div>

    <div class="main-curved-box">
        
        <div class="topbar" id="navbar">
          <div class="brand" style="flex: 1;">
            <a href="index.php">
              <img src="public/assets/images/Pseudonym logo.png" alt="Pseudonym" class="brand-logo">
            </a>
          </div>
          
          <!-- Tengah: Menu Link -->
          <div class="nav-center">
            <a href="about.php" class="nav-text-link">About</a>
            <a href="#" class="nav-text-link">Safety</a>
            <a href="#" class="nav-text-link">Contact us</a>
          </div>

          <!-- Kanan: Tombol Auth (Liquid Glass) + Hamburger (mobile) -->
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
            <a href="about.php" class="mobile-menu-link">About</a>
            <a href="#" class="mobile-menu-link">Safety</a>
            <a href="#" class="mobile-menu-link">Contact us</a>
            <?php if (isset($isOwner) && $isOwner): ?>
              <a href="dashboard.php" class="mobile-menu-link mobile-menu-cta">Kotak Masuk</a>
            <?php elseif (isset($viewer) && $viewer): ?>
              <a href="dashboard.php" class="mobile-menu-link mobile-menu-cta">Kotak Masukmu</a>
            <?php else: ?>
              <a href="auth/google-login.php" class="mobile-menu-link mobile-menu-cta">Masuk</a>
            <?php endif; ?>
          </div>
        </div>

        <!-- Area Konten Hero Utama -->
        <div class="page-wrap">
          <div class="hero">
            <div class="hero-left">
              <span class="eyebrow">pesan tanpa nama pengirim</span>
              <h1 class="serif-heading">Katakan yang tak berani kau ucapkan langsung.</h1>
              <p class="lede">Kirim pesan rahasia ke siapa pun yang punya kotak surat di Pseudonym. Tidak perlu daftar. Identitasmu tidak pernah disimpan.</p>

              <div class="envelope glass-btn">
                <div class="envelope-label">Kirim ke alamat</div>
                <div class="envelope-row">
                  <input type="text" id="username-input" placeholder="ketik username tujuan…" autocomplete="off">
                  <button class="btn-seal" id="go-btn" disabled>Buka Kotak Surat</button>
                </div>
                <div class="lookup-hint" id="lookup-hint"></div>
              </div>

              <div class="divider-word">atau</div>

<!-- Ubah baris cta-row ini -->
      <div class="cta-row">
        <a href="auth/google-login.php" class="btn-outline glass-btn">
          Punya kotak surat sendiri? Masuk dengan Google →
        </a>
      </div>
                </div>

            <div class="hero-visual" aria-hidden="true">
              <div class="big"></div>
              <div class="stack">
                <div class="small"></div>
                <div class="small"></div>
              </div>
            </div>
          </div>
        </div>
        
    </div> <!-- Penutup .main-curved-box -->

    <!-- Footer di area bawah (latar hitam) -->
    <div class="site-footer-dark">
      <div class="footer-grid">
        <div>© Pseudonym</div>
        <div><a href="about.php">About</a> • Help • Privacy</div>
      </div>
    </div>

    <!-- Script JS -->
    <script src="public/assets/js/search-username.js"></script>
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
        // Tutup menu kalau salah satu link diklik
        mobileMenu.querySelectorAll('a').forEach(function(link) {
          link.addEventListener('click', function() {
            mobileMenu.classList.remove('open');
            hamburgerBtn.classList.remove('open');
            hamburgerBtn.setAttribute('aria-expanded', 'false');
          });
        });
        // Tutup menu kalau layar di-resize balik ke ukuran desktop
        window.addEventListener('resize', function() {
          if (window.innerWidth > 780) {
            mobileMenu.classList.remove('open');
            hamburgerBtn.classList.remove('open');
            hamburgerBtn.setAttribute('aria-expanded', 'false');
          }
        });
      }

      // Script Animasi
      window.addEventListener('load', function() {
        const overlay = document.getElementById('intro-overlay');
        if (overlay) {
          setTimeout(function() {
              overlay.classList.add('hidden');
          }, 3500);
        }
      });
    </script>
</body>
</html>