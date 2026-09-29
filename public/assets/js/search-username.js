// ============================================
// SEARCH-USERNAME.JS - PSEUDONYM MVC
// ============================================

(function () {
  const input = document.getElementById('username-input');
  const goBtn = document.getElementById('go-btn');
  const hint = document.getElementById('lookup-hint');
  let debounceTimer = null;
  let foundUsername = null;

  const baseUrl = (window.App && window.App.baseUrl !== undefined) ? window.App.baseUrl : '';
  const apiUrl = (window.App && window.App.apiUrl) ? window.App.apiUrl : (baseUrl + '/api');

  function setHint(text, cls) {
    if (!hint) return;
    hint.textContent = text;
    hint.className = 'lookup-hint' + (cls ? ' ' + cls : '');
  }

  function goToProfile() {
    if (foundUsername) {
      window.location.href = baseUrl + '/u/' + encodeURIComponent(foundUsername);
    }
  }

  if (input) {
    input.addEventListener('input', () => {
      const value = input.value.trim().replace(/^@/, ''); // hapus karakter @ jika user mengetik @
      foundUsername = null;
      if (goBtn) goBtn.disabled = true;

      clearTimeout(debounceTimer);
      if (!value) {
        setHint('');
        return;
      }

      setHint('Mencari kotak surat…');
      debounceTimer = setTimeout(async () => {
        try {
          const res = await fetch(apiUrl + '/check-username?u=' + encodeURIComponent(value));
          if (!res.ok) {
            throw new Error('Gagal memeriksa username');
          }
          const data = await res.json();
          if (data.exists) {
            foundUsername = data.username;
            if (goBtn) goBtn.disabled = false;
            setHint('✓ Ditemukan: @' + data.username, 'found');
          } else {
            setHint('Tidak ada kotak surat dengan username ini.', 'notfound');
          }
        } catch (e) {
          setHint('Gagal memeriksa username. Coba lagi.', 'notfound');
        }
      }, 300);
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && foundUsername) {
        e.preventDefault();
        goToProfile();
      }
    });
  }

  if (goBtn) {
    goBtn.addEventListener('click', (e) => {
      e.preventDefault();
      goToProfile();
    });
  }
})();