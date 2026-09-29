<div class="page-wrap" style="padding-top:80px; padding-bottom:40px;">
    
    <!-- PROFIL HEADER -->
    <div class="profile-header" style="display:flex; align-items:center; gap:16px; margin-bottom:28px; max-width:1100px; margin-left:auto; margin-right:auto; padding:0 16px;">
        <?php if (!empty($owner['avatar_url'])): ?>
            <img src="<?= h($owner['avatar_url']) ?>" class="avatar" alt="" style="width:64px; height:64px; border-radius:50%; object-fit:cover; border:2px solid var(--primary);">
        <?php else: ?>
            <div class="avatar-fallback" style="width:64px; height:64px; border-radius:50%; background:var(--dark-bg-2); border:2px solid var(--primary); display:flex; align-items:center; justify-content:center; font-size:1.5rem; color:var(--primary); font-weight:700;">
                <?= h(strtoupper(substr($owner['username'], 0, 1))) ?>
            </div>
        <?php endif; ?>
        <div>
            <h1 class="serif-heading" style="font-size:1.6rem; margin:0;"><?= h($owner['username']) ?></h1>
            <div class="handle" style="color:var(--muted); font-family:'IBM Plex Mono',monospace; font-size:0.85rem;">@<?= h($owner['username']) ?></div>
        </div>
    </div>

    <!-- GRID: PESAN (Kiri) + FORM KIRIM (Kanan) -->
    <div class="hero profile-grid" style="display:grid; grid-template-columns:1fr 340px; gap:40px; max-width:1100px; margin:0 auto; padding:0 16px; align-items:start;">
        
        <!-- KOLOM KIRI: DAFTAR PESAN -->
        <div>
            <div class="section-label" style="font-family:'IBM Plex Mono',monospace; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:var(--text-muted-dark); margin:0 0 20px; display:flex; align-items:center; gap:10px;">
                <span>📬 Semua Pesan (<?= count($messages) ?>)</span>
                <span style="flex:1; height:1px; background:rgba(17,24,39,0.08);"></span>
            </div>

            <div class="message-list" id="message-list" data-is-owner="<?= $isOwner ? '1' : '0' ?>" data-username="<?= h($owner['username']) ?>" style="margin-bottom:40px;">
                <?php if (empty($messages)): ?>
                    <div class="empty-state" id="empty-state" style="text-align:center; padding:60px 20px; color:var(--muted); border:1px dashed var(--paper-line); border-radius:16px; background:var(--paper-2);">
                        <div style="font-size:3rem; margin-bottom:12px;">📭</div>
                        Belum ada pesan. Jadilah yang pertama mengirim!
                    </div>
                <?php endif; ?>

                <?php foreach ($messages as $msg): ?>
                    <?php $hasReply = !empty($msg['reply_content']); ?>
                    <div class="message-card" data-message-id="<?= (int)$msg['id'] ?>" style="background:var(--paper); border:1px solid var(--paper-line); border-radius:16px; padding:20px 22px; margin-bottom:16px; transition:border-color .2s; <?= $hasReply ? '' : 'border-left:4px solid var(--primary);' ?>">
                        
                        <div class="message-meta" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:8px;">
                            <div>
                                <?php if ($isOwner && (int)$msg['is_hidden']): ?>
                                    <span class="seal-badge hidden" style="display:inline-flex; align-items:center; gap:6px; background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); color:#B91C1C; padding:4px 10px 4px 6px; border-radius:999px; font-size:0.7rem; font-family:'IBM Plex Mono',monospace;">
                                        <span class="dot" style="width:8px; height:8px; border-radius:50%; background:#EF4444;"></span> disembunyikan
                                    </span>
                                <?php endif; ?>
                            </div>
                            <span class="message-time" style="font-family:'IBM Plex Mono',monospace; font-size:0.72rem; color:var(--text-muted-dark);"><?= h(timeAgo($msg['created_at'])) ?></span>
                        </div>

                        <div class="message-content" style="font-size:0.98rem; color:var(--text-dark); white-space:pre-wrap; word-break:break-word;"><?= h($msg['content']) ?></div>

                        <?php if ($hasReply): ?>
                            <div class="reply-block" style="margin-top:14px; padding:14px 16px; background:rgba(201,162,39,0.06); border-left:2px solid var(--primary); border-radius:0 8px 8px 0;">
                                <div class="reply-label" style="font-family:'IBM Plex Mono',monospace; font-size:0.68rem; text-transform:uppercase; letter-spacing:0.08em; color:var(--muted); margin-bottom:6px;">Balasan dari @<?= h($owner['username']) ?></div>
                                <div class="reply-content" style="font-size:0.92rem; white-space:pre-wrap; word-break:break-word;"><?= h($msg['reply_content']) ?></div>
                            </div>

                            <?php if ($isOwner): ?>
                                <div class="social-share-buttons" style="display:flex; gap:8px; margin-top:12px; flex-wrap:wrap;">
                                    <button class="share-btn copy-link" data-action="share" data-type="copy" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; font-size:0.75rem; padding:8px 12px; border-radius:8px; border:none; cursor:pointer; font-weight:600; background:var(--primary); color:var(--dark-bg); transition:all .2s;">📋 Copy Link</button>
                                    <a class="share-btn twitter" href="javascript:void(0)" data-action="share" data-type="twitter" target="_blank" rel="noopener" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; font-size:0.75rem; padding:8px 12px; border-radius:8px; border:none; cursor:pointer; font-weight:600; background:rgba(29,161,242,0.12); color:#1DA1F2; border:1px solid rgba(29,161,242,0.3); text-decoration:none; transition:all .2s;">𝕏 Share</a>
                                    <a class="share-btn whatsapp" href="javascript:void(0)" data-action="share" data-type="whatsapp" target="_blank" rel="noopener" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; font-size:0.75rem; padding:8px 12px; border-radius:8px; border:none; cursor:pointer; font-weight:600; background:rgba(37,211,102,0.12); color:#25D366; border:1px solid rgba(37,211,102,0.3); text-decoration:none; transition:all .2s;">💬 Share</a>
                                    <a class="share-btn facebook" href="javascript:void(0)" data-action="share" data-type="facebook" target="_blank" rel="noopener" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; font-size:0.75rem; padding:8px 12px; border-radius:8px; border:none; cursor:pointer; font-weight:600; background:rgba(59,89,152,0.12); color:#3B5998; border:1px solid rgba(59,89,152,0.3); text-decoration:none; transition:all .2s;">f Share</a>
                                </div>
                            <?php endif; ?>
                        <?php elseif ($isOwner): ?>
                            <button class="reply-toggle" data-action="toggle-reply" style="background:none; border:none; color:var(--primary); font-size:0.82rem; cursor:pointer; padding:4px 0; margin-top:10px; font-weight:500;">+ Balas pesan ini</button>
                            <div class="reply-form" style="margin-top:14px; display:none;">
                                <textarea placeholder="Tulis balasanmu…" maxlength="500" style="width:100%; background:var(--paper-2); border:1.5px solid var(--paper-line); border-radius:8px; padding:10px 12px; color:var(--text-dark); font-size:0.9rem; resize:vertical; min-height:60px; outline:none; font-family:'Inter',sans-serif;"></textarea>
                                <div class="reply-actions" style="display:flex; gap:10px; margin-top:12px;">
                                    <button class="btn-small" data-action="submit-reply" style="font-size:0.82rem; padding:8px 14px; border-radius:8px; border:none; cursor:pointer; background:var(--primary); color:var(--dark-bg); font-weight:600; transition:background .2s;">Kirim Balasan</button>
                                    <button class="btn-small ghost" data-action="cancel-reply" style="font-size:0.82rem; padding:8px 14px; border-radius:8px; border:1.5px solid var(--paper-line); background:transparent; color:var(--text-muted); cursor:pointer; font-weight:600; transition:all .2s;">Batal</button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- KOLOM KANAN: FORM KIRIM PESAN -->
        <aside class="compose-col" style="position:sticky; top:100px; align-self:start;">
            <div class="envelope compose-box" style="background:rgba(255,255,255,0.15); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.2); border-radius:24px; padding:28px; box-shadow:0 8px 32px rgba(0,0,0,0.06), inset 0 1px 0 rgba(255,255,255,0.3);">
                <div style="margin-bottom:12px; font-weight:600; font-size:0.95rem; color:var(--text-dark);">Kirim pesan rahasia ke @<?= h($owner['username']) ?></div>
                <textarea id="message-input" maxlength="500" placeholder="Tulis apa yang ingin kamu sampaikan secara rahasia…" style="width:100%; background:rgba(255,255,255,0.3); backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,0.2); border-radius:16px; padding:16px 18px; font-size:0.98rem; color:var(--text-dark); resize:vertical; min-height:120px; outline:none; transition:all 0.3s ease; font-family:'Inter',sans-serif;"></textarea>
                <div class="compose-footer" style="display:flex; justify-content:space-between; align-items:center; margin-top:14px;">
                    <span class="char-count" style="font-size:0.78rem; color:var(--text-muted); font-family:'IBM Plex Mono',monospace; background:rgba(255,255,255,0.1); padding:4px 12px; border-radius:999px; border:1px solid rgba(255,255,255,0.1);">
                        <span id="char-count">0</span>/500
                    </span>
                    <button class="btn-seal" id="send-btn" style="background:var(--primary); color:var(--dark-bg); border:none; border-radius:14px; padding:12px 32px; font-weight:700; font-size:0.95rem; cursor:pointer; transition:all 0.3s ease; box-shadow:0 4px 20px rgba(204,255,0,0.25);">
                        Kirim Diam-diam
                    </button>
                </div>
            </div>
        </aside>

    </div>
</div>

<script src="<?= asset('js/profile.js') ?>"></script>
