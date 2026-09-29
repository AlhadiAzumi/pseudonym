
<div class="page-wrap">

    <div class="contact-page">

    <!-- HERO SECTION -->
    <section class="contact-hero">
        <h1 class="contact-title">
            Ada pertanyaan?<br>
            <span>Kami siap mendengarkan.</span>
        </h1>

        <p class="contact-intro">
            Punya saran, kendala teknis, atau penawaran kerja sama? Kirimkan pesan Anda dan tim kami akan merespons secepat mungkin.
        </p>
    </section>

    <!-- MAIN CONTACT GRID -->
    <section class="contact-grid">

        <!-- FORM KIRIM PESAN -->
        <div class="contact-form-card">
            <h2>Kirim Pesan</h2>
            <form action="#" method="POST" class="contact-form">
                <div class="form-group">
                    <label for="name">Nama Lengkap / Username</label>
                    <input type="text" id="name" name="name" placeholder="Masukkan nama Anda" required>
                </div>

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" id="email" name="email" placeholder="nama@email.com" required>
                </div>

                <div class="form-group">
                    <label for="category">Kategori Pesan</label>
                    <select id="category" name="category" required>
                        <option value="general">Pertanyaan Umum</option>
                        <option value="bug">Laporan Masalah / Bug</option>
                        <option value="partnership">Kerja Sama & Kemitraan</option>
                        <option value="privacy">Privasi & Keamanan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Pesan Anda</label>
                    <textarea id="message" name="message" rows="5" placeholder="Tuliskan pesan Anda secara detail di sini..." required></textarea>
                </div>

                <button type="submit" class="btn-seal contact-submit-btn">
                    <span>Kirim Pesan</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </form>
        </div>

        <!-- INFO SIDEBAR (BENTO CARD) -->
        <div class="contact-info-sidebar">
            
            <div class="info-card info-dark">
                <div class="info-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <h3>Email Resmi</h3>
                <p>Untuk tanggapan langsung melalui email:</p>
                <a href="mailto:support@pseudonym.com" class="info-link">support@pseudonym.com</a>
            </div>

            <div class="info-card">
                <div class="info-icon icon-light">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3>Waktu Respons</h3>
                <p>Tim kami aktif merespons pesan pada:</p>
                <span class="info-badge">Senin - Jumat | 09.00 - 18.00 WIB</span>
            </div>

            <div class="info-card">
                <div class="info-icon icon-light">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Keamanan & Privasi</h3>
                <p>Ingin melaporkan pelanggaran atau perundungan?</p>
                <a href="<?= url('/safety') ?>" class="info-text-link">Kunjungi Pusat Keamanan →</a>
            </div>

        </div>

    </section>

    </div>
</div>