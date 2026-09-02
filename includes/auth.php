<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $statement = db()->prepare('SELECT id, name, email FROM users WHERE id = ?');
    $statement->execute([$_SESSION['user_id']]);
    return $statement->fetch() ?: null;
}

function require_auth(): void
{
    if (!current_user()) {
        flash('error', 'Faça login para acessar esta página.');
        redirect('login.php');
    }
}
