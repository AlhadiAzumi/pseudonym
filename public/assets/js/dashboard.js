// ============================================
// DASHBOARD.JS - VERSION FIX
// ============================================

(function () {
  const copyBtn = document.getElementById('copy-btn');
  const shareInput = document.getElementById('share-url');

  if (copyBtn && shareInput) {
    copyBtn.addEventListener('click', async () => {
      shareInput.select();
      try {
        await navigator.clipboard.writeText(shareInput.value);
        copyBtn.textContent = 'Tersalin!';
      } catch (e) {
        document.execCommand('copy');
        copyBtn.textContent = 'Tersalin!';
      }
      setTimeout(() => (copyBtn.textContent = 'Salin Tautan'), 1800);
    });
  }

  // ==========================================
  // GENERATE STORY IMAGE (pakai template PNG)
  // ==========================================
  const TEMPLATE_URL = 'assets/images/template-share-story.png';

  function wrapCanvasText(ctx, text, maxWidth) {
    const words = text.split(/\s+/);
    const lines = [];
    let current = '';
    words.forEach((word) => {
      const test = current ? current + ' ' + word : word;
      if (ctx.measureText(test).width > maxWidth && current) {
        lines.push(current);
        current = word;
      } else {
        current = test;
      }
    });
    if (current) lines.push(current);
    return lines;
  }

  function loadImage(src) {
    return new Promise((resolve, reject) => {
      const img = new Image();
      img.onload = () => resolve(img);
      img.onerror = reject;
      img.src = src;
    });
  }

  async function generateShareImage(messageText) {
    const img = await loadImage(TEMPLATE_URL);

    const canvas = document.createElement('canvas');
    canvas.width = img.naturalWidth;
    canvas.height = img.naturalHeight;
    const ctx = canvas.getContext('2d');

    // Gambar template sebagai background
    ctx.drawImage(img, 0, 0);

    // Koordinat kotak hijau kosong — diukur langsung dari file template (1080x1920)
    const boxX = 270;
    const boxY = 1005;
    const boxWidth = 540;
    const boxHeight = 150;

    // Auto-shrink font biar teks panjang tetap muat
    let fontSize = 46;
    let lines;
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    do {
      ctx.font = `700 ${fontSize}px Arial, sans-serif`;
      lines = wrapCanvasText(ctx, messageText, boxWidth - 40);
      if (lines.length * (fontSize * 1.2) <= boxHeight - 16) break;
      fontSize -= 2;
    } while (fontSize > 16);

    const lineHeight = fontSize * 1.2;
    const totalTextHeight = lines.length * lineHeight;
    let ty = boxY + boxHeight / 2 - totalTextHeight / 2 + lineHeight / 2;

    ctx.fillStyle = '#12330f'; // warna teks di atas kotak hijau — ganti kalau kurang kontras
    lines.forEach((line) => {
      ctx.fillText(line, boxX + boxWidth / 2, ty);
      ty += lineHeight;
    });

    return new Promise((resolve) => canvas.toBlob(resolve, 'image/png'));
  }

  const list = document.getElementById('message-list');
  if (!list) return;

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  list.addEventListener('click', async (e) => {
    const target = e.target;
    const action = target?.dataset?.action;
    if (!action) return;

    const card = target.closest('.message-card');
    if (!card) return;

    // ==========================================
    // 1. TOGGLE VISIBILITY
    // ==========================================
    if (action === 'toggle-visibility') {
      const btn = target;
      const currentHidden = btn.dataset.hidden === '1';
      const newHidden = !currentHidden;

      btn.disabled = true;
      btn.textContent = 'Menyimpan…';

      try {
        const res = await fetch('api/toggle-message-visibility.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            message_id: card.dataset.messageId,
            hidden: newHidden
          }),
        });
        const data = await res.json();

        if (!data.success) {
          alert(data.error || 'Gagal mengubah visibilitas pesan.');
          btn.disabled = false;
          btn.textContent = currentHidden ? 'Tampilkan ke publik' : 'Sembunyikan dari publik';
          return;
        }

        // Update UI
        btn.dataset.hidden = data.hidden ? '1' : '0';
        card.dataset.hidden = data.hidden ? '1' : '0';
        btn.textContent = data.hidden ? 'Tampilkan ke publik' : 'Sembunyikan dari publik';
        btn.classList.toggle('active', data.hidden);

        // Update badge
        const badgeContainer = card.querySelector('.message-meta > div:first-child');
        if (badgeContainer) {
          const oldBadge = badgeContainer.querySelector('.seal-badge');
          if (oldBadge) {
            if (data.hidden) {
              oldBadge.className = 'seal-badge hidden';
              oldBadge.innerHTML = '<span class="dot"></span> disembunyikan';
            } else {
              oldBadge.className = 'seal-badge visible';
              oldBadge.innerHTML = '<span class="dot"></span> terlihat publik';
            }
          }
        }
      } catch (err) {
        alert('Terjadi kesalahan jaringan.');
      } finally {
        btn.disabled = false;
      }
      return;
    }

    // ==========================================
    // 2. TOGGLE REPLY FORM
    // ==========================================
    if (action === 'toggle-reply') {
      const replyForm = card.querySelector('.reply-form');
      if (replyForm) {
        if (replyForm.style.display === 'block') {
          replyForm.style.display = 'none';
          target.textContent = '+ Balas pesan ini';
        } else {
          replyForm.style.display = 'block';
          target.textContent = '− Tutup balasan';
          const textarea = replyForm.querySelector('textarea');
          if (textarea) textarea.focus();
        }
      }
      return;
    }

    // ==========================================
    // 3. CANCEL REPLY
    // ==========================================
    if (action === 'cancel-reply') {
      const replyForm = card.querySelector('.reply-form');
      const toggleBtn = card.querySelector('.reply-toggle');
      if (replyForm) {
        replyForm.style.display = 'none';
        const textarea = replyForm.querySelector('textarea');
        if (textarea) textarea.value = '';
      }
      if (toggleBtn) {
        toggleBtn.textContent = '+ Balas pesan ini';
      }
      return;
    }

    // ==========================================
    // 4. SUBMIT REPLY
    // ==========================================
    if (action === 'submit-reply') {
      const replyForm = card.querySelector('.reply-form');
      if (!replyForm) return;

      const textarea = replyForm.querySelector('textarea');
      const content = textarea.value.trim();
      if (!content) {
        alert('Silakan tulis balasan terlebih dahulu.');
        textarea.focus();
        return;
      }

      const btn = target;
      btn.disabled = true;
      btn.textContent = 'Mengirim…';

      try {
        const res = await fetch('api/reply-message.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            message_id: card.dataset.messageId,
            content: content
          }),
        });
        const data = await res.json();

        if (!data.success) {
          alert(data.error || 'Gagal mengirim balasan.');
          btn.disabled = false;
          btn.textContent = 'Kirim Balasan';
          return;
        }

        // Reload page to show new reply
        location.reload();
      } catch (err) {
        alert('Terjadi kesalahan jaringan.');
        btn.disabled = false;
        btn.textContent = 'Kirim Balasan';
      }
      return;
    }

    // ==========================================
    // 4.5 SHARE AS IMAGE (Story)
    // ==========================================
    if (action === 'share-image') {
      const btn = target;
      const messageContent = card.querySelector('.message-content')?.textContent || '';

      const originalText = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Menyiapkan…';

      try {
        const blob = await generateShareImage(messageContent);
        const file = new File([blob], 'pseudonym-story.png', { type: 'image/png' });

        if (navigator.canShare && navigator.canShare({ files: [file] })) {
          await navigator.share({ files: [file], title: 'Pseudonym' });
        } else {
          const url = URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = 'pseudonym-story.png';
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
          URL.revokeObjectURL(url);
          alert('Gambar berhasil didownload! Upload manual ke Story IG kamu.');
        }
      } catch (err) {
        if (err.name !== 'AbortError') {
          alert('Gagal membuat gambar: ' + err.message);
        }
      } finally {
        btn.disabled = false;
        btn.textContent = originalText;
      }
      return;
    }

    // ==========================================
    // 5. SHARE BUTTONS
    // ==========================================
    if (action === 'share') {
      const shareType = target.dataset.type;
      const username = list.dataset.username;
      const messageContent = card.querySelector('.message-content')?.textContent || '';

      // Build URL with message ID
      const messageId = card.dataset.messageId;
      const baseUrl = window.location.origin + '/rahasia-app/u.php';
      const shareUrl = baseUrl + '?u=' + encodeURIComponent(username) + '&m=' + messageId;

      const shareText = `"${messageContent.substring(0, 100)}${messageContent.length > 100 ? '...' : ''}" - @${username}`;

      if (shareType === 'copy') {
        const copyText = `Lihat balasan di story surat ini:\n\n"${messageContent}"\n\n${shareUrl}`;
        try {
          await navigator.clipboard.writeText(copyText);
          const originalText = target.textContent;
          target.textContent = '✓ Disalin!';
          setTimeout(() => {
            target.textContent = originalText;
          }, 2000);
        } catch (err) {
          // Fallback
          const input = document.createElement('input');
          input.value = copyText;
          document.body.appendChild(input);
          input.select();
          document.execCommand('copy');
          document.body.removeChild(input);
          const originalText = target.textContent;
          target.textContent = '✓ Disalin!';
          setTimeout(() => {
            target.textContent = originalText;
          }, 2000);
        }
      } else if (shareType === 'twitter') {
        window.open(
          `https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}&url=${encodeURIComponent(shareUrl)}`,
          'twitter-share',
          'width=550,height=420'
        );
      } else if (shareType === 'whatsapp') {
        window.open(
          `https://wa.me/?text=${encodeURIComponent(shareText + ' ' + shareUrl)}`,
          'whatsapp-share'
        );
      } else if (shareType === 'facebook') {
        window.open(
          `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}&quote=${encodeURIComponent(shareText)}`,
          'facebook-share',
          'width=550,height=420'
        );
      }
      return;
    }
  });
})();