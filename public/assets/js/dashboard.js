// ============================================
// DASHBOARD.JS - PSEUDONYM MVC
// ============================================

(function () {
  const baseUrl = (window.App && window.App.baseUrl !== undefined) ? window.App.baseUrl : '';
  const apiUrl = (window.App && window.App.apiUrl) ? window.App.apiUrl : (baseUrl + '/api');

  // ==========================================
  // 1. COPY SHARE URL
  // ==========================================
  const copyBtn = document.getElementById('copy-btn');
  const shareInput = document.getElementById('share-url');

  if (copyBtn && shareInput) {
    copyBtn.addEventListener('click', async () => {
      shareInput.select();
      try {
        await navigator.clipboard.writeText(shareInput.value);
        copyBtn.textContent = '✓ Tersalin!';
      } catch (e) {
        document.execCommand('copy');
        copyBtn.textContent = '✓ Tersalin!';
      }
      setTimeout(() => (copyBtn.textContent = 'Salin Tautan'), 2000);
    });
  }

  // ==========================================
  // 2. GENERATE 9:16 STORY IMAGE
  // ==========================================
  function wrapCanvasText(ctx, text, maxWidth) {
    const lines = [];
    const paragraphs = String(text || '').split(/\r?\n/);

    paragraphs.forEach((paragraph) => {
      const words = paragraph.trim().split(/\s+/).filter(Boolean);
      let current = '';

      words.forEach((word) => {
        let remaining = word;
        const candidate = current ? current + ' ' + remaining : remaining;

        if (ctx.measureText(candidate).width <= maxWidth) {
          current = candidate;
          return;
        }

        if (current) {
          lines.push(current);
          current = '';
        }

        while (ctx.measureText(remaining).width > maxWidth && remaining.length > 1) {
          let splitAt = 1;
          while (splitAt < remaining.length && ctx.measureText(remaining.slice(0, splitAt + 1)).width <= maxWidth) {
            splitAt += 1;
          }
          lines.push(remaining.slice(0, splitAt));
          remaining = remaining.slice(splitAt);
        }
        current = remaining;
      });

      if (current || !words.length) lines.push(current);
    });

    return lines;
  }

  function fitStoryText(ctx, text, maxWidth, maxHeight, startSize, minSize) {
    const lineHeightRatio = 1.18;

    for (let size = startSize; size >= minSize; size -= 1) {
      ctx.font = `700 ${size}px Arial, sans-serif`;
      const lines = wrapCanvasText(ctx, text, maxWidth);
      const lineHeight = size * lineHeightRatio;
      if (lines.length * lineHeight <= maxHeight) {
        return { size, lines, lineHeight };
      }
    }

    ctx.font = `700 ${minSize}px Arial, sans-serif`;
    const lines = wrapCanvasText(ctx, text, maxWidth);
    const size = Math.max(6, Math.min(minSize, maxHeight / (lines.length * lineHeightRatio)));
    ctx.font = `700 ${size}px Arial, sans-serif`;
    const fittedLines = wrapCanvasText(ctx, text, maxWidth);

    return { size, lines: fittedLines, lineHeight: size * lineHeightRatio };
  }

  function drawCenteredLines(ctx, lines, centerX, centerY, lineHeight) {
    const firstY = centerY - ((lines.length - 1) * lineHeight) / 2;
    lines.forEach((line, index) => ctx.fillText(line, centerX, firstY + index * lineHeight));
  }

  async function generateShareImage(messageText) {
    const templateUrl = baseUrl + '/assets/images/template-share-story.png';
    const template = await new Promise((resolve, reject) => {
      const image = new Image();
      image.onload = () => resolve(image);
      image.onerror = () => reject(new Error('Template Story tidak dapat dimuat.'));
      image.src = templateUrl;
    });

    const width = template.naturalWidth;
    const height = template.naturalHeight;
    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;

    const ctx = canvas.getContext('2d');
    if (!ctx) throw new Error('Browser tidak dapat membuat kanvas Story.');
    ctx.drawImage(template, 0, 0, width, height);

    const message = fitStoryText(ctx, messageText || 'Kirim pesan anonim untuk memulai.', width * 0.45, height * 0.07, width * 0.052, 10);
    ctx.font = `700 ${message.size}px Arial, sans-serif`;
    ctx.fillStyle = '#171717';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    drawCenteredLines(ctx, message.lines, width / 2, height * 0.563, message.lineHeight);

    return new Promise((resolve, reject) => {
      canvas.toBlob((blob) => {
        if (blob) resolve(blob);
        else reject(new Error('Browser gagal menghasilkan gambar Story.'));
      }, 'image/png');
    });
  }

  const list = document.getElementById('message-list');
  if (!list) return;

  list.addEventListener('click', async (e) => {
    const target = e.target;
    const action = target?.dataset?.action;
    if (!action) return;

    const card = target.closest('.message-card');
    if (!card) return;

    // ==========================================
    // TOGGLE VISIBILITY
    // ==========================================
    if (action === 'toggle-visibility') {
      const btn = target;
      const currentHidden = btn.dataset.hidden === '1';
      const newHidden = !currentHidden;

      btn.disabled = true;
      btn.textContent = 'Menyimpan…';

      try {
        const res = await fetch(apiUrl + '/toggle-visibility', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            message_id: card.dataset.messageId,
            hidden: newHidden,
          }),
        });
        const data = await res.json();

        if (!data.success) {
          alert(data.error || 'Gagal mengubah visibilitas pesan.');
          btn.disabled = false;
          btn.textContent = currentHidden ? 'Tampilkan ke publik' : 'Sembunyikan dari publik';
          return;
        }

        btn.dataset.hidden = data.hidden ? '1' : '0';
        card.dataset.hidden = data.hidden ? '1' : '0';
        btn.textContent = data.hidden ? 'Tampilkan ke publik' : 'Sembunyikan dari publik';
        btn.classList.toggle('active', data.hidden);

        const badgeContainer = card.querySelector('.message-meta > div:first-child');
        if (badgeContainer) {
          if (data.hidden) {
            badgeContainer.innerHTML = '<span class="seal-badge hidden" style="display:inline-flex; align-items:center; gap:6px; background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); color:#B91C1C; padding:4px 10px 4px 6px; border-radius:999px; font-size:0.7rem; font-family:\'IBM Plex Mono\',monospace;"><span class="dot" style="width:8px; height:8px; border-radius:50%; background:#EF4444;"></span> disembunyikan</span>';
          } else {
            badgeContainer.innerHTML = '<span class="seal-badge visible" style="display:inline-flex; align-items:center; gap:6px; background:rgba(0,217,126,0.12); border:1px solid rgba(0,217,126,0.3); color:#059669; padding:4px 10px 4px 6px; border-radius:999px; font-size:0.7rem; font-family:\'IBM Plex Mono\',monospace;"><span class="dot" style="width:8px; height:8px; border-radius:50%; background:#00D97E;"></span> terlihat publik</span>';
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
    // TOGGLE REPLY FORM
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
    // CANCEL REPLY
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
    // SUBMIT REPLY
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
        const res = await fetch(apiUrl + '/reply-message', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            message_id: card.dataset.messageId,
            content: content,
          }),
        });
        const data = await res.json();

        if (!data.success) {
          alert(data.error || 'Gagal mengirim balasan.');
          btn.disabled = false;
          btn.textContent = 'Kirim Balasan';
          return;
        }

        location.reload();
      } catch (err) {
        alert('Terjadi kesalahan jaringan.');
        btn.disabled = false;
        btn.textContent = 'Kirim Balasan';
      }
      return;
    }

    // ==========================================
    // SHARE AS IMAGE (Story IG)
    // ==========================================
    if (action === 'share-image') {
      const btn = target;
      const messageContent = card.querySelector('.message-content')?.textContent?.trim() || '';

      const originalText = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Menyiapkan…';

      try {
        const blob = await generateShareImage(messageContent);
        const file = new File([blob], 'pseudonym-story.png', { type: 'image/png' });

        if (navigator.canShare && navigator.canShare({ files: [file] })) {
          await navigator.share({ files: [file], title: 'Pseudonym Story' });
        } else {
          const url = URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = 'pseudonym-story.png';
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
          URL.revokeObjectURL(url);
          alert('Gambar story berhasil didownload! Silakan upload ke Story Instagram kamu. 📸');
        }
      } catch (err) {
        if (err.name !== 'AbortError') {
          alert('Gagal membuat gambar story: ' + err.message);
        }
      } finally {
        btn.disabled = false;
        btn.textContent = originalText;
      }
      return;
    }

    // ==========================================
    // SOCIAL SHARE BUTTONS
    // ==========================================
    if (action === 'share') {
      const shareType = target.dataset.type;
      const username = list.dataset.username;
      const messageContent = card.querySelector('.message-content')?.textContent || '';
      const messageId = card.dataset.messageId;

      const shareUrl = window.location.origin + baseUrl + '/u/' + encodeURIComponent(username) + '?m=' + messageId;
      const shareText = `"${messageContent.substring(0, 100)}${messageContent.length > 100 ? '...' : ''}" - @${username}`;

      if (shareType === 'copy') {
        const copyText = `Lihat pesan dan balasan di kotak suratku:\n\n"${messageContent}"\n\n${shareUrl}`;
        try {
          await navigator.clipboard.writeText(copyText);
          const originalText = target.textContent;
          target.textContent = '✓ Disalin!';
          setTimeout(() => {
            target.textContent = originalText;
          }, 2000);
        } catch (err) {
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