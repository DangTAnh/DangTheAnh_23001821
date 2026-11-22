<?php

ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
require_once __DIR__ . '/dbConnect.php';

function respond(array $body, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

function positiveInteger($value, string $label): int
{
    $number = filter_var($value, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 2147483647],
    ]);
    if ($number === false) {
        respond(['success' => false, 'message' => $label . ' phải là số nguyên dương.'], 400);
    }
    return $number;
}

function normalizeProduct(array $product): array
{
    foreach (['id', 'price', 'stock'] as $key) $product[$key] = (int) $product[$key];
    return $product;
}

function findProduct(PDO $pdo, int $id): ?array
{
    $statement = $pdo->prepare('SELECT id, name, price, stock, image FROM products WHERE id = ?');
    $statement->execute([$id]);
    $row = $statement->fetch();
    return $row ? normalizeProduct($row) : null;
}

function readProduct(): array
{
    $input = json_decode(file_get_contents('php://input'), false, 512, JSON_THROW_ON_ERROR);
    if (!($input instanceof stdClass)) {
        respond(['success' => false, 'message' => 'Dữ liệu phải là một đối tượng JSON.'], 400);
    }
    if (!isset($input->name) || !is_string($input->name)) {
        respond(['success' => false, 'message' => 'Tên sản phẩm phải là chuỗi.'], 422);
    }
    $name = preg_replace('/^\s+|\s+$/u', '', $input->name);
    if (mb_strlen($name, 'UTF-8') < 1 || mb_strlen($name, 'UTF-8') > 100) {
        respond(['success' => false, 'message' => 'Tên sản phẩm phải từ 1 đến 100 ký tự.'], 422);
    }
    if (!isset($input->price) || !is_int($input->price) || $input->price <= 0 || $input->price > 2147483647) {
        respond(['success' => false, 'message' => 'Đơn giá phải là số nguyên từ 1 đến 2.147.483.647 đồng.'], 422);
    }
    if (!isset($input->stock) || !is_int($input->stock) || $input->stock < 0 || $input->stock > 2147483647) {
        respond(['success' => false, 'message' => 'Tồn kho phải là số nguyên từ 0 đến 2.147.483.647.'], 422);
    }
    // Không nhận trường image từ JSON; chỉ upload.php được cập nhật ảnh.
    return ['name' => $name, 'price' => $input->price, 'stock' => $input->stock];
}

function deleteImage(?string $path): void
{
    // Chỉ xóa đúng tên ảnh do ứng dụng sinh ra, trong thư mục uploads.
    if ($path && preg_match('~^uploads/[a-f0-9]{32}\.(jpg|png)$~D', $path)) {
        $file = __DIR__ . '/../' . $path;
        if (is_file($file) && !@unlink($file)) error_log('Không xóa được ảnh cũ: ' . $path);
    }
}
