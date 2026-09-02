<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_auth();
$payload = json_decode(file_get_contents('php://input'), true) ?: [];
if (!hash_equals($_SESSION['csrf'] ?? '', $payload['csrf'] ?? '')) { http_response_code(419); exit; }
$id = filter_var($payload['id'] ?? 0, FILTER_VALIDATE_INT);
$status = $payload['status'] ?? '';
if (!$id || !in_array($status, ['Disponível', 'Vendido', 'Reservado'], true)) { http_response_code(422); exit; }
$query = db()->prepare('UPDATE products SET status=? WHERE id=? AND user_id=?');
$query->execute([$status, $id, current_user()['id']]);
header('X-Status-Class: ' . match ($status) { 'Disponível' => 'status-available', 'Vendido' => 'status-sold', 'Reservado' => 'status-reserved' });
header('Content-Type: application/json');
echo json_encode(['success' => true]);
