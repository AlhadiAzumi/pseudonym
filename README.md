# Pseudonym — Aplikasi Pesan Rahasia (Anonim)

Website ala Secreto/NGL: siapa pun bisa kirim pesan rahasia ke akun terdaftar tanpa perlu daftar,
sedangkan untuk **menerima** pesan harus login pakai Google. Semua pesan + balasan pemilik bisa
dibaca publik, tapi hanya pemilik akun yang bisa membalas.

## Struktur folder

```
Pseudonym/
├── config/
│   ├── database.php      # koneksi PDO ke PostgreSQL / Supabase
│   └── config.php        # APP_URL + kredensial Google OAuth
├── includes/
│   └── functions.php     # helper: auth, sanitasi, response JSON
├── auth/
│   ├── google-login.php     # redirect ke Google
│   ├── google-callback.php  # tukar code -> token -> buat/login user
│   └── logout.php
├── api/
│   ├── send-message.php     # POST publik, kirim pesan anonim
│   ├── reply-message.php    # POST, hanya pemilik akun
│   └── check-username.php   # GET, cek username ada/tidak
├── assets/
│   ├── css/style.css
│   └── js/ (search-username.js, profile.js, dashboard.js)
├── database/schema.sql
├── index.php              # landing page
├── choose-username.php    # onboarding pilih username setelah login Google pertama kali
├── {username}             # profil publik: kirim pesan + lihat semua pesan/balasan
└── dashboard.php          # kotak masuk pemilik akun (butuh login)
```

## 1. Setup Supabase dan XAMPP / Laragon

1. Copy folder `pseudonym` ke `htdocs` (XAMPP) atau `www` (Laragon).
2. Nyalakan **Apache** di XAMPP. Database disediakan Supabase, jadi MySQL lokal tidak perlu dinyalakan.
3. Di Supabase Dashboard, buka **SQL Editor**, jalankan isi file `database/schema.sql`.
4. Salin `.env.example` menjadi `.env`. Dari **Project Settings → Database → Connect**, ambil detail
   koneksi PostgreSQL (pilih **Session pooler** bila koneksi langsung tidak tersedia dari jaringan),
   lalu isi `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, dan `DB_PASS` di `.env`.
   `DB_SSLMODE=require` harus tetap aktif untuk koneksi TLS.
5. Sesuaikan konfigurasi aplikasi di `.env`, contohnya:
   ```dotenv
   APP_URL=https://pseudonym.site
   ```
   Agar URL profil berbentuk `https://pseudonym.site/username`, pasang aplikasi di document root
   domain tersebut dan arahkan domain ke hosting/server aplikasi. Domain saja belum otomatis
   menghubungkan website ke XAMPP lokal.

Pastikan ekstensi PHP `pdo_pgsql` dan `curl` aktif. Jangan unggah `.env` ke Git atau membagikan
kredensial database. Gunakan password database Supabase, bukan anon key atau service-role key.

## 2. Setup Google OAuth (untuk fitur "Login dengan Google")

1. Buka **[Google Cloud Console](https://console.cloud.google.com/)** → login dengan akun Google kamu.
2. Buat project baru (atau pilih project yang sudah ada) lewat dropdown di pojok kiri atas.
3. Di menu kiri, masuk ke **APIs & Services → OAuth consent screen**.
   - Pilih **User Type: External** → Create.
   - Isi *App name* (misalnya "Bisik"), *User support email*, dan *Developer contact email*.
   - Lewati bagian Scopes (klik Save and Continue).
   - Di bagian **Test users**, tambahkan email Google kamu sendiri supaya bisa dipakai login selagi
     app masih status "Testing".
   - Save and Continue sampai selesai.
4. Masuk ke **APIs & Services → Credentials**.
   - Klik **Create Credentials → OAuth client ID**.
   - Application type: **Web application**.
   - Name: bebas, misalnya "Bisik Local".
   - Di **Authorized redirect URIs**, tambahkan persis:
     ```
     https://pseudonym.site/auth/google-callback
     ```
     Samakan URL ini dengan `GOOGLE_REDIRECT_URI` di `.env`.
   - Klik **Create**.
5. Akan muncul popup berisi **Client ID** dan **Client Secret**. Copy keduanya.
6. Buka `config/config.php`, isi:
   ```php
   define('GOOGLE_CLIENT_ID', 'xxxxxxxx.apps.googleusercontent.com');
   define('GOOGLE_CLIENT_SECRET', 'xxxxxxxx');
   ```

Selesai! Sekarang tombol "Masuk dengan Google" di aplikasi akan berfungsi.

> Catatan: selama OAuth consent screen masih status **Testing**, hanya email yang kamu daftarkan
> di "Test users" yang bisa login. Kalau mau dipakai publik nanti, submit app untuk verifikasi
> Google (perlu domain asli, bukan localhost).
<br>
## 3. Coba jalankan

1. Buka `https://pseudonym.site/` di browser setelah domain diarahkan ke hosting/server aplikasi.
2. Klik **"Masuk dengan Google"** → login → kamu akan diminta pilih **username** (ini jadi alamat
   kotak suratmu, misalnya `https://pseudonym.site/budi`).
3. Salin tautan itu (ada tombol "Salin Tautan" di dashboard) dan bagikan ke orang lain / buka di
   tab lain / mode incognito untuk simulasi "orang lain".
4. Di tab incognito, buka tautan tadi, tulis pesan rahasia, kirim — tanpa perlu login.
5. Login lagi sebagai pemilik akun → buka **Kotak Masuk** → balas pesan yang masuk.
6. Buka lagi halaman publik `u.php?u=budi` — pesan dan balasannya terlihat oleh siapa saja, tapi
   hanya pemilik akun yang punya tombol "Balas".

## Alur sesuai kebutuhan

| # | Kebutuhan | Implementasi |
|---|---|---|
| 1 | Siapa pun kirim pesan (>1x) tanpa daftar | `u.php` form kirim → `api/send-message.php`, tidak butuh session |
| 2 | Untuk menerima pesan harus daftar + login Google | Akun hanya dibuat via `auth/google-callback.php`, tidak ada form daftar manual |
| 3 | Semua pesan bisa dibaca orang lain & pemilik | `u.php` menampilkan semua `messages` + `replies` untuk semua pengunjung |
| 4 | Pesan hanya dibalas oleh pemilik akun | `api/reply-message.php` cek `receiver_id === session user_id` |
| 5 | Orang lain cuma baca pesan+balasan, tidak bisa balas | Tombol/form balas (`.reply-toggle`) hanya dirender kalau `isOwner` true |

## Catatan keamanan & pengembangan lanjut

- Anti-spam saat ini masih sederhana (fingerprint hash IP+tanggal, limit 20 pesan/hari per target).
  Untuk produksi, tambahkan rate limiting yang lebih kuat (misalnya captcha).
- Semua output HTML sudah di-escape (`htmlspecialchars`) untuk mencegah XSS.
- Query pakai prepared statement (PDO) untuk mencegah SQL injection.
- Kalau nanti deploy ke hosting/VPS beneran, ganti `APP_URL` ke domain asli, tambahkan HTTPS,
  dan set cookie session `secure` + `httponly`.
