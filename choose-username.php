<?php
require_once __DIR__ . '/includes/functions.php';

$user = currentUser();
if (!$user) {
    header('Location: ' . APP_URL . '/auth/google-login.php');
    exit;
}
if (!empty($user['username'])) {
    header('Location: ' . APP_URL . '/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');

    if (!isValidUsername($username)) {
        $error = 'Username harus 3-20 karakter, hanya huruf/angka/underscore.';
    } else {
        $stmt = getDB()->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Username sudah dipakai orang lain. Coba yang lain.';
        } else {
            $stmt = getDB()->prepare('UPDATE users SET username = ? WHERE id = ?');
            $stmt->execute([$username, $user['id']]);
            header('Location: ' . APP_URL . '/dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pilih Username — Pseudonym</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-page">
  <div class="paper-card auth-card">
    <h1 class="serif-heading">Pilih alamat kotak suratmu</h1>
    <p class="muted">Ini akan jadi tautan yang kamu bagikan supaya orang bisa mengirim pesan rahasia untukmu.</p>

    <?php if ($error): ?>
      <div class="alert-error"><?= h($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="stacked-form">
      <label for="username">Username</label>
      <div class="input-prefix">
        <!-- <span><?= h(APP_URL) ?>/u.php?u=</span> -->
        <input type="text" id="username" name="username" placeholder="Masukkan username" required
               pattern="[a-zA-Z0-9_]{3,20}" value="<?= h($_POST['username'] ?? '') ?>">
      </div>
      <button type="submit" class="btn-seal">Kunci Username Ini</button>
    </form>
  </div>
</div>
</body>
</html>
