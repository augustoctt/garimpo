<?php
require_once __DIR__ . '/includes/auth.php';
if (current_user()) redirect('dashboard.php');
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $statement = db()->prepare('SELECT id, name, email, password FROM users WHERE email = ?');
    $statement->execute([$email ?: '']);
    $user = $statement->fetch();
    if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
        session_regenerate_id(true); $_SESSION['user_id'] = $user['id']; redirect('dashboard.php');
    }
    $error = 'E-mail ou senha inválidos.';
}
$pageTitle = 'Entrar'; require __DIR__ . '/partials/header.php';
?>
<div class="auth-shell"><div class="auth-brand"><span class="brand-mark"><i data-lucide="sparkles"></i></span><h1 class="page-title mt-3">Seu acervo, no lugar.</h1><p class="text-muted">Entre para cuidar do seu brechó.</p></div>
<div class="panel"><h2 class="h5 mb-4">Acessar conta</h2><?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?><form method="post" data-validate novalidate><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><div class="mb-3"><label class="form-label" for="email">E-mail</label><input class="form-control" id="email" type="email" name="email" required value="<?= old('email') ?>"><div class="invalid-feedback">Informe um e-mail válido.</div></div><div class="mb-3"><label class="form-label" for="password">Senha</label><input class="form-control" id="password" type="password" name="password" required minlength="6"><div class="invalid-feedback">Informe sua senha.</div></div><button class="btn btn-primary w-100" type="submit">Entrar</button></form><div class="d-flex justify-content-between mt-4 small"><a href="register.php">Criar conta</a><a href="forgot.php">Esqueci a senha</a></div></div></div>
<?php require __DIR__ . '/partials/footer.php'; ?>
