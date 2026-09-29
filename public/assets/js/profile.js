// ============================================
// PROFILE.JS - PSEUDONYM MVC
// ============================================

(function () {
  const baseUrl = (window.App && window.App.baseUrl !== undefined) ? window.App.baseUrl : '';
  const apiUrl = (window.App && window.App.apiUrl) ? window.App.apiUrl : (baseUrl + '/api');

  const list = document.getElementById('message-list');
  const msgInput = document.getElementById('message-input');
  const charCount = document.getElementById('char-count');
  const sendBtn = document.getElementById('send-btn');

  function showSendSuccess() {
    const dialog = document.createElement('dialog');
    dialog.className = 'send-success-dialog';
    dialog.setAttribute('aria-labelledby', 'send-success-title');
    dialog.innerHTML = `
      <div class="send-success-dialog__mark" aria-hidden="true">&#10003;</div>
      <p class="send-success-dialog__eyebrow">PESAN TERKIRIM</p>
      <h2 id="send-success-title">Rahasiamu sudah sampai.</h2>
      <p class="send-success-dialog__copy">Pesan anonim berhasil dikirim. Terima kasih sudah berani menyampaikannya.</p>
      <button class="btn-seal send-success-dialog__button" type="button">Kembali</button>
    `;
    document.body.appendChild(dialog);
    dialog.addEventListener('close', () => location.reload(), { once: true });
    dialog.querySelector('button').addEventListener('click', () => dialog.close());
    dialog.showModal();
  }

  // Character counter
  if (msgInput && charCount) {
    msgInput.addEventListener('input', () => {
      charCount.textContent = msgInput.value.length;
    });
  }

  // ==========================================
  // 1. SEND ANONYMOUS MESSAGE
  // ==========================================
  if (sendBtn && msgInput && list) {
    const username = list.dataset.username;

    sendBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      const content = msgInput.value.trim();
      if (!content) {
        alert('Silakan tulis pesan terlebih dahulu.');
        msgInput.focus();
        return;
      }

      sendBtn.disabled = true;
      sendBtn.textContent = 'Mengirim…';

      try {
        const res = await fetch(apiUrl + '/send-message', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ username, content }),
        });
        const data = await res.json();

        if (!data.success) {
          alert(data.error || 'Gagal mengirim pesan.');
          sendBtn.disabled = false;
          sendBtn.textContent = 'Kirim Diam-diam';
          return;
        }

        showSendSuccess();
      } catch (e) {
        alert('Terjadi kesalahan jaringan.');
        sendBtn.disabled = false;
        sendBtn.textContent = 'Kirim Diam-diam';
      }
    });

    // Enter key untuk submit (Shift+Enter untuk new line)
    msgInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendBtn.click();
      }
    });
  }

  // ==========================================
  // 2. OWNER ACTIONS (Reply & Share)
  // ==========================================
  if (list && list.dataset.isOwner === '1') {
    const username = list.dataset.username;

    list.addEventListener('click', async (e) => {
      const target = e.target;
      const action = target?.dataset?.action;
      if (!action) return;

      const card = target.closest('.message-card');
      if (!card) return;

      // Toggle Reply
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

      // Cancel Reply
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

      // Submit Reply
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

      // Share Actions
      if (action === 'share') {
        const shareType = target.dataset.type;
        const messageContent = card.querySelector('.message-content')?.textContent || '';
        const replyContent = card.querySelector('.reply-content')?.textContent || '';
        const messageId = card.dataset.messageId;

        const shareUrl = window.location.origin + baseUrl + '/u/' + encodeURIComponent(username) + '?m=' + messageId;
        const shareText = `"${messageContent.substring(0, 100)}${messageContent.length > 100 ? '...' : ''}" - @${username}`;

        if (shareType === 'copy') {
          const copyText = `Lihat balasan pesan di kotak suratku:\n\n"${messageContent}"\n\n${replyContent ? 'Balasan: ' + replyContent + '\n\n' : ''}${shareUrl}`;
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
  }
})();