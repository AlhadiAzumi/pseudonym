<?php
$pageTitle = $title ?? 'Pseudonym — Kirim Pesan Rahasia Anonim';
$pageDescription = $metaDescription ?? 'Kirim pesan rahasia anonim ke siapa pun dengan aman di Pseudonym. Tanpa perlu daftar akun, privasi dan identitas pengirim 100% terjaga.';
$currentUrl = full_url($_SERVER['REQUEST_URI'] ?? '/');
$ogImageUrl = full_url('/assets/images/Pseudonym%20logo.png');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?></title>
    <meta name="description" content="<?= h($pageDescription) ?>">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#0F172A">
    <link rel="canonical" href="<?= h($currentUrl) ?>">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Pseudonym">
    <meta property="og:url" content="<?= h($currentUrl) ?>">
    <meta property="og:title" content="<?= h($pageTitle) ?>">
    <meta property="og:description" content="<?= h($pageDescription) ?>">
    <meta property="og:image" content="<?= h($ogImageUrl) ?>">
    <meta property="og:locale" content="id_ID">

    <!-- Twitter / X -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= h($pageTitle) ?>">
    <meta name="twitter:description" content="<?= h($pageDescription) ?>">
    <meta name="twitter:image" content="<?= h($ogImageUrl) ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/Pseudonym%20logo%20browser.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/Pseudonym%20logo%20browser.png') ?>">

    <!-- Font Optimization (Preconnect & Non-blocking) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap">
    </noscript>

    <!-- Preload Critical Logo Asset -->
    <link rel="preload" as="image" href="<?= asset('images/Pseudonym%20logo.png') ?>">

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">

    <!-- window.App harus di <head> agar tersedia sebelum semua script body -->
    <script>
    window.App = {
        baseUrl: '<?= rtrim(base_url(), '/') ?>',
        apiUrl: '<?= url('/api') ?>'
    };
    </script>
</head>
<body>