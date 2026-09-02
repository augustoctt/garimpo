<?php
declare(strict_types=1);

function product_categories(): array
{
    return [
        '100' => ['name' => 'Roupas', 'icon' => 'shirt', 'subcategories' => ['101' => 'Camisas', '102' => 'Calças', '103' => 'Vestidos', '104' => 'Casacos', '105' => 'Saias']],
        '200' => ['name' => 'Calçados', 'icon' => 'footprints', 'subcategories' => ['201' => 'Tênis', '202' => 'Botas', '203' => 'Sandálias', '204' => 'Sapatos', '205' => 'Chinelos']],
        '300' => ['name' => 'Bolsas', 'icon' => 'briefcase-business', 'subcategories' => ['301' => 'Bolsas de mão', '302' => 'Mochilas', '303' => 'Clutches', '304' => 'Carteiras']],
        '400' => ['name' => 'Acessórios', 'icon' => 'gem', 'subcategories' => ['401' => 'Joias', '402' => 'Bijuterias', '403' => 'Óculos', '404' => 'Cintos', '405' => 'Lenços']],
        '500' => ['name' => 'Casa e decoração', 'icon' => 'lamp-floor', 'subcategories' => ['501' => 'Móveis', '502' => 'Iluminação', '503' => 'Quadros', '504' => 'Cozinha', '505' => 'Têxteis']],
        '600' => ['name' => 'Livros e papelaria', 'icon' => 'book-open', 'subcategories' => ['601' => 'Literatura', '602' => 'Didáticos', '603' => 'Revistas', '604' => 'Cadernos']],
        '700' => ['name' => 'Eletrônicos', 'icon' => 'radio', 'subcategories' => ['701' => 'Áudio', '702' => 'Celulares', '703' => 'Informática', '704' => 'Câmeras']],
        '800' => ['name' => 'Esportes e lazer', 'icon' => 'trophy', 'subcategories' => ['801' => 'Fitness', '802' => 'Futebol', '803' => 'Ciclismo', '804' => 'Camping']],
        '900' => ['name' => 'Infantil', 'icon' => 'toy-brick', 'subcategories' => ['901' => 'Roupas infantis', '902' => 'Brinquedos', '903' => 'Livros infantis', '904' => 'Bebê']],
    ];
}

function product_sizes(): array { return ['PP', 'P', 'M', 'G', 'GG', '36', '37', '38', '39', '40', '41', '42', 'Único', 'Não se aplica']; }
function product_conditions(): array { return ['Novo', 'Excelente', 'Muito boa', 'Boa', 'Com marcas de uso', 'Para restauração']; }
function product_origins(): array { return ['Doação', 'Consignação', 'Compra', 'Troca', 'Acervo próprio']; }

function product_subcategories(): array
{
    $subcategories = [];
    foreach (product_categories() as $category) foreach ($category['subcategories'] as $code => $name) $subcategories[$code] = $name;
    return $subcategories;
}

function status_class(string $status): string
{
    return match ($status) {
        'Disponível' => 'status-available',
        'Vendido' => 'status-sold',
        'Reservado' => 'status-reserved',
        default => 'status-neutral',
    };
}

function category_icon(string $category): string
{
    foreach (product_categories() as $item) if ($item['name'] === $category) return $item['icon'];
    return 'sparkles';
}

function next_product_code(PDO $database, int $userId, string $category, string $subcategory): string
{
    $categoryCode = '999';
    foreach (product_categories() as $code => $item) {
        if ($item['name'] === $category) { $categoryCode = array_key_exists($subcategory, $item['subcategories']) ? $subcategory : $code . '0'; break; }
    }
    $statement = $database->prepare('SELECT code FROM products WHERE user_id=? AND code LIKE ? ORDER BY id DESC LIMIT 1');
    $statement->execute([$userId, $categoryCode . '.%']);
    $lastCode = $statement->fetchColumn();
    $sequence = $lastCode ? ((int) substr((string) $lastCode, 4)) + 1 : 1;
    return $categoryCode . '.' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
}
