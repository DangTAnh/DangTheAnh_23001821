<?php

ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

require_once __DIR__ . '/../common/dbConnect.php';

function respond(array $body, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

function readProduct(): array
{
    $input = json_decode(file_get_contents('php://input'), false, 512, JSON_THROW_ON_ERROR);
    if (!($input instanceof stdClass)) {
        respond(['success' => false, 'message' => 'Dữ liệu phải là một đối tượng JSON.'], 400);
    }

    if (!isset($input->name) || !is_string($input->name)) {
        respond(['success' => false, 'message' => 'Tên sản phẩm phải là chuỗi từ 1 đến 100 ký tự.'], 422);
    }
    $name = preg_replace('/^\s+|\s+$/u', '', $input->name);
    $length = mb_strlen($name, 'UTF-8');
    if ($length < 1 || $length > 100) {
        respond(['success' => false, 'message' => 'Tên sản phẩm sau khi bỏ khoảng trắng phải từ 1 đến 100 ký tự.'], 422);
    }
    if (!isset($input->price) || !is_int($input->price) || $input->price <= 0 || $input->price > 2147483647) {
        respond(['success' => false, 'message' => 'Đơn giá phải là số nguyên từ 1 đến 2.147.483.647 đồng.'], 422);
    }
    if (!isset($input->stock) || !is_int($input->stock) || $input->stock < 0 || $input->stock > 2147483647) {
        respond(['success' => false, 'message' => 'Tồn kho phải là số nguyên từ 0 đến 2.147.483.647.'], 422);
    }

    return ['name' => $name, 'price' => $input->price, 'stock' => $input->stock];
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'], true)) {
        header('Allow: GET, POST, PUT, DELETE');
        respond(['success' => false, 'message' => 'Phương thức không được hỗ trợ.'], 405);
    }

    $id = null;
    if ($method === 'PUT' || $method === 'DELETE') {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 2147483647],
        ]);
        if ($id === false) {
            respond(['success' => false, 'message' => 'Mã sản phẩm không hợp lệ.'], 400);
        }
    }

    // Kiểm tra JSON và dữ liệu trước khi gửi truy vấn ghi vào MySQL.
    $product = ($method === 'POST' || $method === 'PUT') ? readProduct() : null;
    $pdo = dbConnect();

    if ($method === 'GET') {
        $products = $pdo->query('SELECT id, name, price, stock FROM products ORDER BY id')->fetchAll();
        foreach ($products as &$row) {
            $row['id'] = (int) $row['id'];
            $row['price'] = (int) $row['price'];
            $row['stock'] = (int) $row['stock'];
        }
        unset($row);
        respond(['success' => true, 'data' => $products]);
    }

    if ($method === 'POST') {
        $statement = $pdo->prepare('INSERT INTO products (name, price, stock) VALUES (:name, :price, :stock)');
        $statement->execute($product);
        $product['id'] = (int) $pdo->lastInsertId();
        respond(['success' => true, 'message' => 'Đã thêm sản phẩm.', 'data' => $product], 201);
    }

    if ($method === 'PUT') {
        $statement = $pdo->prepare('UPDATE products SET name = :name, price = :price, stock = :stock WHERE id = :id');
        $statement->execute($product + ['id' => $id]);
        // rowCount() bằng 0 cũng có thể do người dùng lưu lại giá trị cũ.
        if ($statement->rowCount() === 0) {
            $check = $pdo->prepare('SELECT id FROM products WHERE id = ?');
            $check->execute([$id]);
            if (!$check->fetch()) {
                respond(['success' => false, 'message' => 'Không tìm thấy sản phẩm cần sửa.'], 404);
            }
        }
        respond(['success' => true, 'message' => 'Đã cập nhật sản phẩm.', 'data' => $product + ['id' => $id]]);
    }

    $statement = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $statement->execute([$id]);
    if ($statement->rowCount() === 0) {
        respond(['success' => false, 'message' => 'Không tìm thấy sản phẩm cần xóa.'], 404);
    }
    respond(['success' => true, 'message' => 'Đã xóa sản phẩm.']);
} catch (JsonException $exception) {
    respond(['success' => false, 'message' => 'JSON không hợp lệ.'], 400);
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    respond(['success' => false, 'message' => 'Không thể truy cập dữ liệu. Kiểm tra MySQL và nhập file database.sql.'], 500);
} catch (Throwable $exception) {
    error_log($exception->getMessage());
    respond(['success' => false, 'message' => 'Có lỗi khi xử lý yêu cầu.'], 500);
}
