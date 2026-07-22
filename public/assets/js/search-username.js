// ============================================
// SEARCH-USERNAME.JS
// ============================================

(function () {
  const input = document.getElementById('username-input');
  const goBtn = document.getElementById('go-btn');
  const hint = document.getElementById('lookup-hint');
  let debounceTimer = null;
  let foundUsername = null;

  function setHint(text, cls) {
    hint.textContent = text;
    hint.className = 'lookup-hint' + (cls ? ' ' + cls : '');
  }

  if (input) {
    input.addEventListener('input', () => {
      const value = input.value.trim();
      foundUsername = null;
      if (goBtn) goBtn.disabled = true;

      clearTimeout(debounceTimer);
      if (!value) {
        setHint('');
        return;
      }

      setHint('mencari…');
      debounceTimer = setTimeout(async () => {
        try {
          const res = await fetch('api/check-username.php?u=' + encodeURIComponent(value));
          const data = await res.json();
          if (data.exists) {
            foundUsername = data.username;
            if (goBtn) goBtn.disabled = false;
            setHint('Ditemukan: @' + data.username, 'found');
          } else {
            setHint('Tidak ada kotak surat dengan username ini.', 'notfound');
          }
        } catch (e) {
          setHint('Gagal memeriksa. Coba lagi.', 'notfound');
        }
      }, 350);
    });
  }

  function goToProfile() {
    if (foundUsername) {
      window.location.href = 'u.php?u=' + encodeURIComponent(foundUsername);
    }
  }

  if (goBtn) {
    goBtn.addEventListener('click', goToProfile);
  }
  
  if (input) {
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && foundUsername) goToProfile();
    });
  }
})();