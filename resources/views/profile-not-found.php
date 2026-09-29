<div class="page-wrap" style="min-height:70vh; display:flex; align-items:center; justify-content:center;">
    <div class="empty-state" style="text-align:center; padding:60px 20px; color:var(--muted);">
        <div style="font-size:3.5rem; margin-bottom:16px;">🔍</div>
        <h2 class="serif-heading" style="margin-bottom:8px;">Kotak Surat Tidak Ditemukan</h2>
        <p style="margin-bottom:24px;">Kotak surat "@<?= h($username) ?>" tidak terdaftar atau belum dibuat.</p>
        <a href="<?= url('/') ?>" class="btn-outline" style="border:1.5px solid var(--primary); background:transparent; color:var(--primary); padding:12px 24px; border-radius:10px; font-weight:600; text-decoration:none; display:inline-block;">&larr; Kembali ke Beranda</a>
    </div>
</div>
