<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
if (current_user()) redirect('dashboard.php');
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    if (strlen($name) < 3 || !$email || strlen($password) < 6) $error = 'Preencha os campos corretamente (senha mínima de 6 caracteres).';
    else { try { $statement = db()->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)'); $statement->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]); flash('success', 'Conta criada. Agora faça login.'); redirect('login.php'); } catch (PDOException $exception) { $error = $exception->getCode() === '23000' ? 'Este e-mail já está cadastrado.' : 'Não foi possível criar a conta.'; } }
}
$pageTitle = 'Criar conta'; require __DIR__ . '/partials/header.php';
?><div class="auth-shell"><div class="auth-brand"><span class="brand-mark"><svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m12 3-1.2 4.8L6 9l4.8 1.2L12 15l1.2-4.8L18 9l-4.8-1.2L12 3Z"></path><path d="m19 15-.6 2.4L16 18l2.4.6L19 21l.6-2.4L22 18l-2.4-.6L19 15Z"></path></svg></span><h1 class="page-title mt-3">Comece por aqui.</h1></div><div class="panel"><h2 class="h5 mb-4">Nova conta</h2><?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?><form method="post" data-validate novalidate><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><div class="mb-3"><label class="form-label">Nome completo</label><input class="form-control" name="name" required minlength="3" value="<?= old('name') ?>"><div class="invalid-feedback">Informe seu nome.</div></div><div class="mb-3"><label class="form-label">E-mail</label><input class="form-control" type="email" name="email" required value="<?= old('email') ?>"><div class="invalid-feedback">Informe um e-mail válido.</div></div><div class="mb-4"><label class="form-label">Senha</label><input class="form-control" type="password" name="password" required minlength="6"><div class="invalid-feedback">Use pelo menos 6 caracteres.</div></div><button class="btn btn-primary w-100">Criar conta</button></form><p class="text-center mt-4 mb-0 small"><a href="login.php">Já tenho uma conta</a></p></div></div><?php require __DIR__ . '/partials/footer.php'; ?>
