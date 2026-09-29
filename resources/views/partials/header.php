<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title ?? 'Pseudonym — Pesan Rahasia Anonim') ?></title>
    
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="icon" type="image/png" href="<?= asset('images/Pseudonym%20logo%20browser.png') ?>">

    <!-- window.App harus di <head> agar tersedia sebelum semua script body -->
    <script>
    window.App = {
        baseUrl: '<?= rtrim(base_url(), '/') ?>',
        apiUrl: '<?= url('/api') ?>'
    };
    </script>
</head>
<body>