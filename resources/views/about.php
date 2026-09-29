<!-- ABOUT PAGE -->
    <div class="page-wrap">

        <div class="about-page">

            <!-- HERO SECTION -->
            <section class="about-hero">
                <h1 class="about-title">
                    Berbicara bebas,<br>
                    <span>tanpa batas identitas.</span>
                </h1>

                <p class="about-intro">
                    Pseudonym adalah platform olah pesan anonim yang dirancang untuk kejujuran total. Sederhana, aman, dan memberi Anda kendali penuh tanpa prasangka.
                </p>
            </section>

            <!-- BENTO OVERVIEW SECTION -->
            <section class="about-bento-grid">
                <div class="bento-card bento-main">
                    <div class="about-card-label">
                        <span class="badge-num">01</span>
                        FILOSOFI KAMI
                    </div>
                    <h2>Ruang aman untuk berkata <em>jujur.</em></h2>
                    <p>
                        Tidak semua pesan mudah disampaikan secara langsung. Terkadang, rasa takut akan penilaian orang lain menghalangi apresiasi, kritik membangun, atau cerita yang sejujurnya.
                    </p>
                    <p>
                        Pseudonym hadir menjembatani hal tersebut. Bagikan tautan personal Anda, biarkan audiens berbicara jujur, dan kelola semua pesan dalam satu wadah yang aman.
                    </p>
                </div>

                <div class="bento-card bento-stat">
                    <div class="stat-number">100%</div>
                    <div class="stat-desc">Privasi Terjaga</div>
                    <p class="stat-detail">Sistem kami tidak melacak identitas pengirim pesan Anda.</p>
                </div>
            </section>

            <!-- FEATURES SECTION -->
            <section class="about-features">
                <div class="about-section-heading">
                    <span class="about-eyebrow">KEUNGGULAN UTAMA</span>
                    <h2>Dibuat sederhana,<br>untuk hal yang berarti.</h2>
                </div>

                <div class="about-feature-grid">
                    <div class="about-feature-card">
                        <div class="feature-header">
                            <div class="feature-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </div>
                            <span class="feature-number">01</span>
                        </div>
                        <h3>Anonimitas Mutlak</h3>
                        <p>Pengirim dapat menyampaikan pesan secara jujur tanpa perlu melampirkan profil atau data pribadi.</p>
                    </div>

                    <div class="about-feature-card">
                        <div class="feature-header">
                            <div class="feature-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                            </div>
                            <span class="feature-number">02</span>
                        </div>
                        <h3>Tanpa Registrasi Pengirim</h3>
                        <p>Siapa pun dapat mengirim pesan secara instan hanya dengan membuka tautan milik Anda.</p>
                    </div>

                    <div class="about-feature-card">
                        <div class="feature-header">
                            <div class="feature-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </div>
                            <span class="feature-number">03</span>
                        </div>
                        <h3>Kontrol Penuh Kotak Masuk</h3>
                        <p>Anda memegang kendali atas pesan yang masuk: sembunyikan, balas, atau bagikan ke publik.</p>
                    </div>

                    <div class="about-feature-card">
                        <div class="feature-header">
                            <div class="feature-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            </div>
                            <span class="feature-number">04</span>
                        </div>
                        <h3>Berekspresi Bebas</h3>
                        <p>Wadah tepat untuk menerima saran transparan, apresiasi rahasia, atau pertanyaan jujur.</p>
                    </div>
                </div>
            </section>

            <!-- HOW IT WORKS -->
            <section class="about-how">
                <div class="about-card-label">
                    <span class="badge-num">02</span>
                    ALUR KERJA
                </div>

                <h2>Sesederhana <em>3 langkah.</em></h2>

                <div class="about-steps">
                    <div class="about-step">
                        <div class="step-num">01</div>
                        <div class="step-content">
                            <h3>Bagikan Tautan Anda</h3>
                            <p>Dapatkan username unik Anda lalu sebarkan link ke media sosial atau teman Anda.</p>
                        </div>
                    </div>

                    <div class="about-step">
                        <div class="step-num">02</div>
                        <div class="step-content">
                            <h3>Pengirim Menulis Pesan</h3>
                            <p>Orang lain dapat langsung menulis tanggapan tanpa hambatan pembuatan akun.</p>
                        </div>
                    </div>

                    <div class="about-step">
                        <div class="step-num">03</div>
                        <div class="step-content">
                            <h3>Kelola Pesan Masuk</h3>
                            <p>Baca pesan di dashboard pribadi Anda dan pilih pesan mana yang ingin Anda publikasikan.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CLOSING CTA -->
            <section class="about-closing">
                <h2>Siap mendengar apa kata mereka?</h2>
                <p>Buat tautan pesan anonim Anda sekarang dalam waktu kurang dari satu menit.</p>
                <?php if (!empty($user)): ?>
                    <a href="<?= url('/dashboard') ?>" class="btn-seal about-cta-btn">Buka Kotak Masuk</a>
                <?php else: ?>
                    <a href="<?= url('/auth/google-login') ?>" class="btn-seal about-cta-btn">Mulai Buat Akun</a>
                <?php endif; ?>
            </section>

        </div>

    </div>