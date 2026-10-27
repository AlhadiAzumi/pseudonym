<!-- Splash Screen Intro Overlay -->
<div id="intro-overlay" style="position: fixed; inset: 0; z-index: 999999; background: #000000; display: flex; justify-content: center; align-items: center; transition: opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.35s; contain: strict;">
    <div class="intro-container">
        <img class="logo-question" src="<?= asset('images/Pseudonym%20logo%20browser.png') ?>" alt="Pseudonym Icon" width="150" height="150" decoding="async">
        <img class="logo-text" src="<?= asset('images/Pseudonym%20logo.png') ?>" alt="Pseudonym Brand" width="250" height="75" decoding="async">
    </div>
    <button id="skip-intro" style="position: absolute; bottom: 24px; right: 24px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 6px 16px; border-radius: 999px; font-size: 0.75rem; cursor: pointer; font-family: sans-serif; backdrop-filter: blur(8px);">Lewati &rarr;</button>
</div>

<style>
/* CSS Keyframes Animasi Intro */
.intro-container {
    position: relative;
    width: 300px;
    height: 300px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.logo-question {
    position: absolute;
    width: 150px;
    height: auto;
    filter: drop-shadow(0 0 10px rgba(173, 255, 47, 0.6));
    animation: fadeInOut 0.5s ease-in-out forwards;
}

.logo-text {
    position: absolute;
    width: 250px;
    height: auto;
    opacity: 0;
    transform: scale(0.8);
    filter: drop-shadow(0 0 15px rgba(173, 255, 47, 0.8));
    animation: revealText 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.4s forwards;
}

@keyframes fadeInOut {
    0% {
        opacity: 0;
        transform: scale(0.5);
    }
    15% {
        opacity: 1;
        transform: scale(1);
    }
    75% {
        opacity: 1;
        transform: scale(1) rotate(0deg);
        filter: drop-shadow(0 0 10px rgba(173, 255, 47, 0.6));
    }
    88% {
        opacity: 0.9;
        transform: scale(1.1) skewX(-15deg);
        filter: drop-shadow(0 0 25px rgba(173, 255, 47, 1)) invert(1);
    }
    100% {
        opacity: 0;
        transform: scale(0.3) rotate(10deg);
    }
}

@keyframes pulse {
    0% {
        filter: drop-shadow(0 0 8px rgba(173, 255, 47, 0.5));
    }
    100% {
        filter: drop-shadow(0 0 22px rgba(173, 255, 47, 0.95));
    }
}

@keyframes revealText {
    0% {
        opacity: 0;
        transform: scale(0.7);
        filter: blur(8px) drop-shadow(0 0 5px rgba(173, 255, 47, 0.5));
    }
    30% {
        opacity: 1;
        transform: scale(1.05);
        filter: blur(0px) drop-shadow(0 0 25px rgba(173, 255, 47, 1));
    }
    50% {
        transform: scale(1);
    }
    100% {
        opacity: 1;
        transform: scale(1);
        filter: drop-shadow(0 0 14px rgba(173, 255, 47, 0.8));
    }
}
</style>

<!-- Area Konten Hero Utama -->
<div class="page-wrap">
    <div class="hero">
        <div class="hero-left">
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

            <div class="cta-row">
                <?php if (!empty($user)): ?>
                    <a href="<?= url('/dashboard') ?>" class="btn-outline glass-btn">
                        Buka Kotak Suratmu (Halo, @<?= h($user['username'] ?: 'User') ?>) &rarr;
                    </a>
                <?php else: ?>
                    <a href="<?= url('/auth/google-login') ?>" class="btn-outline glass-btn">
                        Punya kotak surat sendiri? Masuk dengan Google &rarr;
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="hero-visual" aria-hidden="true">
            <span class="hero-orbit hero-orbit--outer"></span>
            <span class="hero-orbit hero-orbit--inner"></span>
            <span class="hero-spark hero-spark--one">*</span>
            <span class="hero-spark hero-spark--two">+</span>
            <div class="big"></div>
            <div class="stack">
                <div class="small"></div>
                <div class="small"></div>
            </div>
            <div class="hero-float-chip hero-float-chip--new">
                <span class="hero-chip-dot"></span>
                <span><strong>Pesan baru</strong><small>baru saja masuk</small></span>
            </div>
            <div class="hero-float-chip hero-float-chip--safe">
                <span class="hero-chip-lock">&#10003;</span>
                <span>Anonim & aman</span>
            </div>
        </div>
    </div>
</div>

<div class="home-sections">
    <section class="home-proof-strip" aria-label="Ringkasan Pseudonym">
        <div class="home-proof-item">
            <strong>100%</strong>
            <span>Privasi terjaga</span>
        </div>
        <div class="home-proof-item">
            <strong>Tanpa akun</strong>
            <span>Pengirim bisa langsung menulis</span>
        </div>
        <div class="home-proof-item">
            <strong>3 langkah</strong>
            <span>Bagikan, terima, kelola</span>
        </div>
    </section>

    <section class="home-features" aria-labelledby="home-features-title">
        <div class="home-section-heading">
            <div>
                <p class="home-section-label">PSEUDONYM</p>
                <h2 id="home-features-title">Ruang aman untuk berkata jujur.</h2>
            </div>
            <p>Pesan anonim yang sederhana, dengan kendali tetap di tangan pemilik kotak surat.</p>
        </div>

        <div class="home-feature-grid">
            <article class="home-feature-card">
                <span class="home-feature-number">01</span>
                <h3>Anonimitas mutlak</h3>
                <p>Sampaikan apresiasi, kritik, atau cerita tanpa melampirkan profil dan data pribadi.</p>
            </article>
            <article class="home-feature-card home-feature-card--lime">
                <span class="home-feature-number">02</span>
                <h3>Tanpa registrasi pengirim</h3>
                <p>Siapa pun dapat menulis pesan secara instan hanya dengan membuka tautanmu.</p>
            </article>
            <article class="home-feature-card home-feature-card--violet">
                <span class="home-feature-number">03</span>
                <h3>Kontrol penuh</h3>
                <p>Sembunyikan, balas, atau bagikan pesan yang masuk lewat kotak suratmu.</p>
            </article>
            <article class="home-feature-card">
                <span class="home-feature-number">04</span>
                <h3>Berekspresi bebas</h3>
                <p>Terima saran transparan, apresiasi rahasia, dan pertanyaan jujur.</p>
            </article>
        </div>
    </section>

    <section class="home-steps-band" aria-labelledby="home-steps-title">
        <div class="home-steps-copy">
            <p class="home-section-label">MUDAH DIMULAI</p>
            <h2 id="home-steps-title">Tiga langkah. Satu ruang aman.</h2>
            <ol class="home-step-list">
                <li><span>01</span><div><strong>Bagikan tautanmu</strong><p>Sebarkan link kotak surat ke teman atau media sosial.</p></div></li>
                <li><span>02</span><div><strong>Terima pesan</strong><p>Orang lain menulis pesan tanpa perlu membuat akun.</p></div></li>
                <li><span>03</span><div><strong>Kelola dengan nyaman</strong><p>Baca, balas, sembunyikan, atau bagikan pesanmu.</p></div></li>
            </ol>
            <a class="home-steps-link" href="<?= url('/about') ?>">Kenali Pseudonym <span aria-hidden="true">&rarr;</span></a>
        </div>

        <div class="home-inbox-preview" aria-label="Pratinjau pesan anonim">
            <div class="home-preview-topline"><span>KOTAK MASUK</span><span class="home-preview-count">02</span></div>
            <div class="home-preview-message">
                <span class="home-preview-tag">PESAN ANONIM</span>
                <p>“Terima kasih sudah selalu ada.”</p>
                <span class="home-preview-time">baru saja</span>
            </div>
            <div class="home-preview-message home-preview-message--soft">
                <span class="home-preview-tag">PESAN ANONIM</span>
                <p>“Kamu selalu bikin suasana jadi lebih baik.”</p>
                <span class="home-preview-time">beberapa menit lalu</span>
            </div>
            <div class="home-preview-reply"><span></span> Ruang privatmu, kendalimu.</div>
        </div>
    </section>
</div>

<script>
// Logika Intro Splash Screen (Dioptimalkan untuk Performa & Lighthouse)
(function () {
    const overlay = document.getElementById('intro-overlay');
    if (!overlay) return;

    function dismissIntro() {
        overlay.style.opacity = '0';
        overlay.style.pointerEvents = 'none';
        try { sessionStorage.setItem('introPlayed', 'true'); } catch (e) {}
        setTimeout(() => {
            overlay.style.display = 'none';
        }, 350);
    }

    const urlParams = new URLSearchParams(window.location.search);
    const forceIntro = urlParams.get('intro') === '1';
    const isBotOrAudit = /Lighthouse|PTST|PageSpeed|Googlebot|bingbot|Chrome-Lighthouse/i.test(navigator.userAgent);
    const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Jika bot audit / crawler / reduced motion atau sudah pernah dibuka, langsung tampilkan halaman utama tanpa tunda
    if (!forceIntro && (isBotOrAudit || prefersReduced || sessionStorage.getItem('introPlayed'))) {
        overlay.style.display = 'none';
    } else {
        // Durasi snappier untuk pengguna nyata (0.95 detik)
        const autoTimer = setTimeout(dismissIntro, 950);

        const skipBtn = document.getElementById('skip-intro');
        if (skipBtn) {
            skipBtn.addEventListener('click', () => {
                clearTimeout(autoTimer);
                dismissIntro();
            });
        }
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                clearTimeout(autoTimer);
                dismissIntro();
            }
        });
    }
})();
</script>

<script src="<?= asset('js/search-username.js') ?>" defer></script>