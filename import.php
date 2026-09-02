<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/catalog.php';
require_auth();
$user = current_user();
$message = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Selecione um arquivo CSV válido.';
    } elseif (strtolower(pathinfo($_FILES['csv']['name'], PATHINFO_EXTENSION)) !== 'csv') {
        $errors[] = 'O arquivo precisa ter extensão .csv.';
    } else {
        $handle = fopen($_FILES['csv']['tmp_name'], 'r');
        $headers = $handle ? fgetcsv($handle, 0, ';') : false;
        $expected = ['name', 'category_code', 'subcategory_code', 'size', 'condition_grade', 'price', 'entry_date', 'origin', 'status', 'notes'];
        if (!$headers || array_map('trim', $headers) !== $expected) {
            $errors[] = 'Cabeçalho inválido. Use o modelo CSV disponível nesta página.';
        } else {
            try {
                db()->beginTransaction();
                $line = 1; $imported = 0;
                while (($row = fgetcsv($handle, 0, ';')) !== false) {
                    $line++;
                    if (count($row) !== count($expected) || count(array_filter($row, static fn ($value) => trim((string) $value) !== '')) === 0) continue;
                    $data = array_combine($expected, array_map('trim', $row));
                    $category = $categories[$data['category_code']] ?? null;
                    $subcategory = $category['subcategories'][$data['subcategory_code']] ?? null;
                    if (!$category || !$subcategory || !in_array($data['size'], product_sizes(), true) || !in_array($data['condition_grade'], product_conditions(), true) || !in_array($data['origin'], product_origins(), true) || !in_array($data['status'], ['Disponível', 'Vendido', 'Reservado'], true) || !is_numeric(str_replace(',', '.', $data['price'])) || !$data['entry_date'] || strlen($data['name']) < 2) {
                        $errors[] = "Linha {$line}: confira categoria, subcategoria, tamanho, condição, origem, status, preço e nome.";
                        continue;
                    }
                    $price = str_replace(',', '.', $data['price']);
                    $code = next_product_code(db(), (int) $user['id'], $category['name'], $data['subcategory_code']);
                    $query = db()->prepare('INSERT INTO products (code,name,category,subcategory,size,condition_grade,price,entry_date,origin,status,notes,user_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
                    $query->execute([$code, $data['name'], $category['name'], $subcategory, $data['size'], $data['condition_grade'], $price, $data['entry_date'], $data['origin'], $data['status'], $data['notes'], $user['id']]);
                    $imported++;
                }
                fclose($handle);
                if ($errors) { db()->rollBack(); } else { db()->commit(); $message = "{$imported} peça(s) importada(s) com sucesso."; }
            } catch (Throwable $exception) {
                if (db()->inTransaction()) db()->rollBack();
                $errors[] = 'Não foi possível concluir a importação.';
            }
        }
    }
}
$pageTitle = 'Importar estoque'; require __DIR__ . '/partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4"><div><div class="eyebrow">Entrada de dados</div><h1 class="page-title h3 mb-0">Importar estoque</h1></div><a href="clients.php" class="btn btn-outline-secondary"><i data-lucide="arrow-left"></i> Estoque</a></div>
<div class="row g-4"><div class="col-lg-7"><div class="panel"><h2 class="h5">Envie seu arquivo CSV</h2><p class="text-muted small">Use ponto e vírgula como separador e mantenha o cabeçalho exatamente igual ao modelo.</p><?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?><?php if ($errors): ?><div class="alert alert-danger"><strong>Importação não realizada.</strong><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?= csrf_token() ?>"><label class="form-label" for="csv">Arquivo CSV</label><input class="form-control mb-3" id="csv" name="csv" type="file" accept=".csv,text/csv" required><button class="btn btn-primary"><i data-lucide="upload"></i> Importar peças</button></form></div></div><div class="col-lg-5"><div class="panel"><h2 class="h5">Modelo de colunas</h2><p class="small text-muted">O sistema gera o código Dewey automaticamente. Os códigos de categoria e subcategoria estão no modelo.</p><a class="btn btn-outline-secondary" download="modelo-estoque.csv" href="modelo-estoque.csv"><i data-lucide="download"></i> Baixar modelo CSV</a></div></div></div>
<?php require __DIR__ . '/partials/footer.php'; ?>
