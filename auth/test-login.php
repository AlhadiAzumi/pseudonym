<?php
/**
 * Test Login — Hanya untuk development/testing tanpa Google OAuth
 * JANGAN gunakan di production!
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $action = $_POST['action'] ?? '';
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email harus valid';
    } else {
        $db = getDB();
        
        if ($action === 'create') {
            // Buat user baru untuk testing
            $name = trim($_POST['name'] ?? 'Test User');
            try {
                $stmt = $db->prepare('INSERT INTO users (email, google_id, display_name) VALUES (?, ?, ?)');
                $stmt->execute([$email, 'test_' . bin2hex(random_bytes(8)), $name]);
                $userId = $db->lastInsertId();
                $_SESSION['user_id'] = $userId;
                $success = "User '$name' dibuat! Redirect ke profile...";
                header('Location: ' . APP_URL . '/choose-username.php', true, 303);
                exit;
            } catch (Exception $e) {
                $error = 'User sudah ada atau error: ' . $e->getMessage();
            }
        } else {
            // Login user yang sudah ada
            $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $success = "Login berhasil! Redirect...";
                header('Location: ' . APP_URL . '/dashboard.php', true, 303);
                exit;
            } else {
                $error = 'User tidak ditemukan. Buat user baru?';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .test-login { max-width: 400px; margin: 80px auto; background: var(--paper); padding: 30px; border-radius: 12px; }
        .test-login h1 { margin-top: 0; color: #241F19; }
        .test-login label { display: block; margin-top: 14px; font-size: 0.85rem; color: #6B6152; font-weight: 600; }
        .test-login input { width: 100%; padding: 10px; margin-top: 4px; border: 1px solid var(--paper-line); border-radius: 6px; }
        .test-login button { width: 100%; margin-top: 18px; padding: 10px; background: #374151; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .test-login button:hover { background: #1f2937; }
        .test-login .error { color: #b91c1c; margin: 12px 0; }
        .test-login .success { color: #4A7A5A; margin: 12px 0; }
        .test-login .warning { background: #FEF3C7; padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 0.85rem; color: #92400E; }
    </style>
</head>
<body>
<div class="test-login">
    <h1>Test Login</h1>
    <div class="warning">⚠️ <strong>Development Only!</strong> Endpoint ini hanya untuk testing. Jangan gunakan di production.</div>
    
    <?php if (!empty($error)): ?>
        <div class="error"><?= h($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="success"><?= h($success) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <label>Email</label>
        <input type="email" name="email" required placeholder="test@example.com">
        
        <label>Nama (jika buat user baru)</label>
        <input type="text" name="name" placeholder="Nama User">
        
        <button type="submit" name="action" value="login">Login Existing User</button>
        <button type="submit" name="action" value="create">Buat User Baru & Login</button>
    </form>
</div>
</body>
</html>
