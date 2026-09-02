<?php
$user = current_user();
$flashMessage = get_flash();
$pageTitle = $pageTitle ?? APP_NAME;
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($pageTitle) ?> | <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark app-nav">
    <div class="container"><a class="navbar-brand" href="<?= $user ? 'dashboard.php' : 'login.php' ?>"><span class="brand-mark"><i data-lucide="sparkles"></i></span> Garimpo Brechó</a>
        <?php if ($user): ?><div class="d-flex align-items-center gap-3"><div class="d-none d-md-flex gap-3"><a class="text-white text-decoration-none small" href="dashboard.php">Painel</a><a class="text-white text-decoration-none small" href="clients.php">Estoque</a><a class="text-white text-decoration-none small" href="import.php">Importar</a><a class="text-white text-decoration-none small" href="contacts.php">Contatos</a><a class="text-white text-decoration-none small" href="report.php">Relatórios</a></div><span class="nav-user">Olá, <?= e(explode(' ', $user['name'])[0]) ?></span><a class="btn btn-sm btn-outline-light" href="logout.php">Sair</a></div><?php endif; ?>
    </div>
</nav>
<main class="container py-4">
<?php if ($flashMessage): ?><div class="alert alert-<?= e($flashMessage['type'] === 'success' ? 'success' : 'danger') ?> alert-dismissible fade show" role="alert"><?= e($flashMessage['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
