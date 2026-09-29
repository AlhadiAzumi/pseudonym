<div class="page-wrap" style="padding-top:80px; padding-bottom:40px;">
    <div style="max-width: 820px; margin: 0 auto; padding: 0 16px;">
        
        <span class="eyebrow">kotak masuk</span>
        <h1 class="serif-heading" style="font-size: 2.2rem;">Halo, <?= h($user['username']) ?></h1>
        <p class="muted" style="margin-bottom: 28px;">Ini semua pesan yang masuk ke kotak suratmu. Hanya kamu yang bisa membalas.</p>

        <!-- SHARE BOX -->
        <div class="share-box" style="background: var(--paper-2); border: 1px solid var(--paper-line); border-radius: 12px; padding: 12px 16px; margin-bottom: 28px; display:flex; gap:10px; align-items:center;">
            <input type="text" readonly value="<?= h($profileUrl) ?>" id="share-url" style="flex:1; background:transparent; border:none; outline:none; font-family:'IBM Plex Mono',monospace; font-size:0.85rem; color:var(--text-dark);">
            <button class="copy-btn" id="copy-btn" style="background:var(--primary); color:var(--dark-bg); border:none; padding:8px 16px; border-radius:8px; font-weight:600; cursor:pointer; font-family:'Inter',sans-serif;">Salin Tautan</button>
        </div>

        <!-- STATS ROW -->
        <div class="stats-row" style="display:grid; grid-template-columns: repeat(3,1fr); gap:12px; margin-bottom:32px;">
            <div class="stat-box" style="background:var(--paper); border:1px solid var(--paper-line); border-radius:16px; padding:20px; text-align:center;">
                <div class="num" style="font-family:'Poppins',sans-serif; font-size:2rem; color:var(--primary); font-weight:700;"><?= (int)$total ?></div>
                <div class="label" style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em; font-family:'IBM Plex Mono',monospace;">Total Pesan</div>
            </div>
            <div class="stat-box" style="background:var(--paper); border:1px solid var(--paper-line); border-radius:16px; padding:20px; text-align:center;">
                <div class="num" style="font-family:'Poppins',sans-serif; font-size:2rem; color:#10B981; font-weight:700;"><?= (int)$replied ?></div>
                <div class="label" style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em; font-family:'IBM Plex Mono',monospace;">Dibalas</div>
            </div>
            <div class="stat-box" style="background:var(--paper); border:1px solid var(--paper-line); border-radius:16px; padding:20px; text-align:center;">
                <div class="num" style="font-family:'Poppins',sans-serif; font-size:2rem; color:#F59E0B; font-weight:700;"><?= (int)$waiting ?></div>
                <div class="label" style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em; font-family:'IBM Plex Mono',monospace;">Menunggu</div>
            </div>
        </div>

        <!-- SECTION LABEL -->
        <div class="section-label" style="font-family:'IBM Plex Mono',monospace; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; color:var(--text-muted-dark); margin:32px 0 20px; display:flex; align-items:center; gap:10px;">
            <span>📬 Semua Pesan (<?= count($messages) ?>)</span>
            <span style="flex:1; height:1px; background:rgba(17,24,39,0.08);"></span>
        </div>

        <!-- MESSAGE LIST -->
        <div class="message-list" id="message-list" data-is-owner="1" data-username="<?= h($user['username']) ?>" style="margin-bottom:40px;">
            <?php if (empty($messages)): ?>
                <div class="empty-state" style="text-align:center; padding:60px 20px; color:var(--muted); border:1px dashed var(--paper-line); border-radius:16px; background:var(--paper-2);">
                    <div style="font-size:3rem; margin-bottom:12px;">📭</div>
                    Belum ada pesan masuk. Bagikan tautan di atas supaya orang bisa mengirim pesan rahasia untukmu.
                </div>
            <?php endif; ?>

            <?php foreach ($messages as $msg): ?>
                <?php $hasReply = !empty($msg['reply_content']); ?>
                <div class="message-card" data-message-id="<?= (int)$msg['id'] ?>" style="background:var(--paper); border:1px solid var(--paper-line); border-radius:16px; padding:20px 22px; margin-bottom:16px; transition:border-color .2s; <?= $hasReply ? '' : 'border-left:4px solid var(--primary);' ?>">
                    
                    <div class="message-meta" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:8px;">
                        <div>
                            <?php if ((int)$msg['is_hidden']): ?>
                                <span class="seal-badge hidden" style="display:inline-flex; align-items:center; gap:6px; background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); color:#B91C1C; padding:4px 10px 4px 6px; border-radius:999px; font-size:0.7rem; font-family:'IBM Plex Mono',monospace;">
                                    <span class="dot" style="width:8px; height:8px; border-radius:50%; background:#EF4444;"></span> disembunyikan
                                </span>
                            <?php else: ?>
                                <span class="seal-badge visible" style="display:inline-flex; align-items:center; gap:6px; background:rgba(0,217,126,0.12); border:1px solid rgba(0,217,126,0.3); color:#059669; padding:4px 10px 4px 6px; border-radius:999px; font-size:0.7rem; font-family:'IBM Plex Mono',monospace;">
                                    <span class="dot" style="width:8px; height:8px; border-radius:50%; background:#00D97E;"></span> terlihat publik
                                </span>
                            <?php endif; ?>
                        </div>
                        <span class="message-time" style="font-family:'IBM Plex Mono',monospace; font-size:0.72rem; color:var(--text-muted-dark);"><?= h(timeAgo($msg['created_at'])) ?></span>
                    </div>

                    <div class="message-content" style="font-size:0.98rem; color:var(--text-dark); white-space:pre-wrap; word-break:break-word;"><?= h($msg['content']) ?></div>

                    <div class="reply-actions" style="display:flex; gap:10px; margin-top:12px; flex-wrap:wrap;">
                        <button class="btn-small visibility-toggle <?= (int)$msg['is_hidden'] ? 'active' : '' ?>" data-action="toggle-visibility" data-hidden="<?= (int)$msg['is_hidden'] ?>" style="font-size:0.82rem; padding:8px 14px; border-radius:8px; border:1.5px solid var(--primary); background:transparent; color:var(--primary); cursor:pointer; font-weight:600; transition:all .2s;">
                            <?= (int)$msg['is_hidden'] ? 'Tampilkan ke publik' : 'Sembunyikan dari publik' ?>
                        </button>
                        <button class="btn-small share-image-btn" data-action="share-image" style="font-size:0.82rem; padding:8px 14px; border-radius:8px; border:1.5px solid var(--paper-line); background:transparent; color:var(--text-dark); cursor:pointer; font-weight:600; transition:all .2s;">
                            🖼️ Share ke Story
                        </button>
                    </div>

                    <?php if ($hasReply): ?>
                        <div class="reply-block" style="margin-top:14px; padding:14px 16px; background:rgba(201,162,39,0.06); border-left:2px solid var(--primary); border-radius:0 8px 8px 0;">
                            <div class="reply-label" style="font-family:'IBM Plex Mono',monospace; font-size:0.68rem; text-transform:uppercase; letter-spacing:0.08em; color:var(--muted); margin-bottom:6px;">Balasanmu</div>
                            <div class="reply-content" style="font-size:0.92rem; white-space:pre-wrap; word-break:break-word;"><?= h($msg['reply_content']) ?></div>
                        </div>

                        <div class="social-share-buttons" style="display:flex; gap:8px; margin-top:12px; flex-wrap:wrap;">
                            <button class="share-btn copy-link" data-action="share" data-type="copy" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; font-size:0.75rem; padding:8px 12px; border-radius:8px; border:none; cursor:pointer; font-weight:600; background:var(--primary); color:var(--dark-bg); transition:all .2s;">📋 Copy Link</button>
                            <a class="share-btn twitter" href="javascript:void(0)" data-action="share" data-type="twitter" target="_blank" rel="noopener" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; font-size:0.75rem; padding:8px 12px; border-radius:8px; border:none; cursor:pointer; font-weight:600; background:rgba(29,161,242,0.12); color:#1DA1F2; border:1px solid rgba(29,161,242,0.3); text-decoration:none; transition:all .2s;">𝕏 Share</a>
                            <a class="share-btn whatsapp" href="javascript:void(0)" data-action="share" data-type="whatsapp" target="_blank" rel="noopener" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; font-size:0.75rem; padding:8px 12px; border-radius:8px; border:none; cursor:pointer; font-weight:600; background:rgba(37,211,102,0.12); color:#25D366; border:1px solid rgba(37,211,102,0.3); text-decoration:none; transition:all .2s;">💬 Share</a>
                            <a class="share-btn facebook" href="javascript:void(0)" data-action="share" data-type="facebook" target="_blank" rel="noopener" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; font-size:0.75rem; padding:8px 12px; border-radius:8px; border:none; cursor:pointer; font-weight:600; background:rgba(59,89,152,0.12); color:#3B5998; border:1px solid rgba(59,89,152,0.3); text-decoration:none; transition:all .2s;">f Share</a>
                        </div>
                    <?php else: ?>
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
</div>

<script src="<?= asset('js/dashboard.js') ?>"></script>
