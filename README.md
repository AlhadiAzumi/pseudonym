# Pseudonym: Aplikasi Pesan Rahasia (Anonim)

Website ala Secreto/NGL: siapa pun bisa kirim pesan rahasia ke akun terdaftar tanpa perlu daftar,
sedangkan untuk **menerima** pesan harus login pakai Google. Pesan baru hanya terlihat oleh
pemilik akun. Pemilik dapat membalas dan memilih menampilkan pesan beserta balasannya di profil publik.

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
   Skema ini juga membuat tabel `contact_messages` untuk menyimpan pesan dari formulir Contact.
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
6. Isi kredensial OAuth di `.env`:
   ```dotenv
   GOOGLE_CLIENT_ID=xxxxxxxx.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=xxxxxxxx
   GOOGLE_REDIRECT_URI=https://pseudonym.site/auth/google-callback
   ```
   Jangan simpan kredensial OAuth di file yang dikomit ke repository.

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
4. Di tab incognito, buka tautan tadi, tulis dan kirim pesan rahasia tanpa perlu login.
5. Login lagi sebagai pemilik akun → buka **Kotak Masuk** → balas pesan yang masuk.
6. Pesan baru hanya terlihat oleh pemilik akun. Dari dashboard, pemilik bisa membalas dan memilih
   pesan mana yang akan ditampilkan bersama balasannya di halaman profil publik.
7. Pengunjung dapat melaporkan pesan publik melalui profil. Pemilik dapat menghapus pesan beserta
   balasannya dari dashboard. Laporan tersimpan di tabel `message_reports` dengan status `pending`.

Jika database Supabase sudah dibuat sebelumnya, jalankan ulang `database/schema.sql` di SQL Editor
untuk membuat tabel laporan moderasi yang baru.

## Alur sesuai kebutuhan

| # | Kebutuhan | Implementasi |
|---|---|---|
| 1 | Siapa pun kirim pesan (>1x) tanpa daftar | `u.php` form kirim → `api/send-message.php`, tidak butuh session |
| 2 | Untuk menerima pesan harus daftar + login Google | Akun hanya dibuat via `auth/google-callback.php`, tidak ada form daftar manual |
| 3 | Pesan yang dipilih pemilik bisa dibaca publik | Pesan baru privat secara default; hanya pesan yang ditampilkan pemilik muncul di profil |
| 4 | Pesan hanya dibalas oleh pemilik akun | `api/reply-message.php` cek `receiver_id === session user_id` |
| 5 | Orang lain cuma baca pesan+balasan, tidak bisa balas | Tombol/form balas (`.reply-toggle`) hanya dirender kalau `isOwner` true |

## Pengujian

Jalankan `composer install`, lalu `composer test`. Pengujian validasi username, fingerprint,
dan pemeriksaan origin berjalan tanpa database. Pengujian hitung pesan memerlukan database
PostgreSQL khusus test dengan skema `database/schema.sql`; atur `TEST_DB_HOST`, `TEST_DB_PORT`,
`TEST_DB_NAME`, `TEST_DB_USER`, `TEST_DB_PASS`, dan `TEST_DB_SSLMODE` sebelum menjalankan test.
Test database harus terpisah dari database produksi karena test membuat fixture di dalam transaksi.

### Mengisi data demo

Seeder berikut menambahkan lima pesan contoh (termasuk satu balasan) ke akun yang sudah ada.
Seeder tidak membuat akun baru dan hanya menghapus/memperbarui pesan yang dibuat oleh seeder untuk
akun tersebut. Jalankan dari PowerShell di folder proyek. Pastikan username benar; ini menulis ke
database yang dikonfigurasi dalam `.env`.

```powershell
# Tambahkan sementara ke .env
ALLOW_DUMMY_SEEDING=true

# Jalankan dengan username akun tujuan
php scripts/seed-demo.php --username=hadi --confirm
```

Setelah berhasil, hapus kembali `ALLOW_DUMMY_SEEDING` dari `.env`. Seeder menolak `APP_ENV=production`
dan tidak bisa dipanggil melalui browser.

### Dashboard administrasi

Set `ADMIN_EMAIL` ke email Google admin pada `.env` lokal dan environment variable hosting. Hanya
akun Google dengan email persis tersebut yang dapat membuka `/admin`. Dashboard menampilkan laporan
pesan dan Contact Us; admin bisa meninjau/mendismiss laporan, menyembunyikan pesan terlapor, atau
menghapus entri laporan. Pesan Contact Us saat ini hanya dapat dibaca dari dashboard.

CSS sumber dibagi ke `resources/css/modules/` dan tetap digabung dalam urutan cascade lama.
Setelah mengubah modul, jalankan `npm install` lalu `npm run build:css` untuk membuat stylesheet
produksi ter-minify di `public/assets/css/style.css`.

## Catatan keamanan & pengembangan lanjut

- Anti-spam memakai fingerprint IP harian; batas harian dan per jam berlaku global per pengirim,
  dengan batas tambahan per penerima. Atur salt acak melalui `APP_FINGERPRINT_SALT`.
- Semua endpoint POST memvalidasi `Origin`/`Referer` same-origin dan token CSRF sesi.
- Aset statis menggunakan query versi berbasis hash isi file sehingga aman dicache satu tahun.
- Semua output HTML sudah di-escape (`htmlspecialchars`) untuk mencegah XSS.
- Query pakai prepared statement (PDO) untuk mencegah SQL injection.
- Header keamanan dipasang lewat `vercel.json` dan `.htaccess`; pastikan HTTPS aktif di hosting.
