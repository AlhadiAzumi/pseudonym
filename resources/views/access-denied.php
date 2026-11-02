<main class="page-wrap access-denied-page">
    <section class="access-denied-card" aria-labelledby="access-denied-title">
        <div class="access-denied-art" aria-hidden="true">
            <span class="access-denied-orbit access-denied-orbit--one"></span>
            <span class="access-denied-orbit access-denied-orbit--two"></span>
            <span class="access-denied-spark access-denied-spark--one">✦</span>
            <span class="access-denied-spark access-denied-spark--two">✳</span>
            <div class="access-denied-lock">
                <span class="access-denied-lock__shackle"></span>
                <span class="access-denied-lock__body">
                    <span class="access-denied-lock__keyhole"></span>
                </span>
            </div>
            <span class="access-denied-code">403</span>
        </div>

        <p class="access-denied-eyebrow">Area terbatas</p>
        <h1 class="serif-heading" id="access-denied-title">Ups, ini bukan kotakmu.</h1>
        <p class="access-denied-copy">
            Halaman administrasi hanya untuk pemilik akses. Tenang, kamu tetap bisa lanjut menjelajahi Pseudonym.
        </p>

        <div class="access-denied-actions">
            <a class="access-denied-button access-denied-button--primary" href="<?= url('/dashboard') ?>">
                Kembali ke kotak masuk <span aria-hidden="true">→</span>
            </a>
            <a class="access-denied-button access-denied-button--secondary" href="<?= url('/') ?>">
                Ke beranda
            </a>
        </div>
        <p class="access-denied-note">Kalau kamu merasa seharusnya punya akses, hubungi administrator.</p>
    </section>
</main>
