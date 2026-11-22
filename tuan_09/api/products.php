<?php

require_once __DIR__ . '/../common/api.php';
$pdo = null;

try {
    $method = $_SERVER['REQUEST_METHOD'];
    if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'], true)) {
        header('Allow: GET, POST, PUT, DELETE');
        respond(['success' => false, 'message' => 'Phương thức không được hỗ trợ.'], 405);
    }

    if ($method === 'GET') {
        $page = positiveInteger($_GET['page'] ?? '1', 'page');
        $limit = positiveInteger($_GET['limit'] ?? '5', 'limit');
        if ($limit !== 5) respond(['success' => false, 'message' => 'limit phải bằng 5.'], 400);

        $pdo = dbConnect();
        $total = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
        $totalPages = max(1, (int) ceil($total / $limit));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $limit;
        $statement = $pdo->prepare('SELECT id, name, price, stock, image FROM products ORDER BY id DESC LIMIT :limit OFFSET :offset');
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        respond([
            'success' => true,
            'data' => array_map('normalizeProduct', $statement->fetchAll()),
            'pagination' => ['page' => $page, 'limit' => $limit, 'total' => $total, 'total_pages' => $totalPages],
        ]);
    }

    $id = null;
    if ($method === 'PUT' || $method === 'DELETE') $id = positiveInteger($_GET['id'] ?? null, 'Mã sản phẩm');
    $product = ($method === 'POST' || $method === 'PUT') ? readProduct() : null;
    $pdo = dbConnect();

    if ($method === 'POST') {
        $statement = $pdo->prepare('INSERT INTO products (name, price, stock) VALUES (:name, :price, :stock)');
        $statement->execute($product);
        $id = (int) $pdo->lastInsertId();
        respond(['success' => true, 'message' => 'Đã thêm sản phẩm.', 'data' => findProduct($pdo, $id)], 201);
    }

    if ($method === 'PUT') {
        $statement = $pdo->prepare('UPDATE products SET name = :name, price = :price, stock = :stock WHERE id = :id');
        $statement->execute($product + ['id' => $id]);
        $saved = findProduct($pdo, $id);
        if (!$saved) respond(['success' => false, 'message' => 'Không tìm thấy sản phẩm cần sửa.'], 404);
        respond(['success' => true, 'message' => 'Đã cập nhật sản phẩm.', 'data' => $saved]);
    }

    // Khóa dòng để việc xóa sản phẩm không chạy đồng thời với thay ảnh.
    $pdo->beginTransaction();
    $statement = $pdo->prepare('SELECT image FROM products WHERE id = ? FOR UPDATE');
    $statement->execute([$id]);
    $old = $statement->fetch();
    if (!$old) {
        $pdo->rollBack();
        respond(['success' => false, 'message' => 'Không tìm thấy sản phẩm cần xóa.'], 404);
    }
    $statement = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $statement->execute([$id]);
    $pdo->commit();
    deleteImage($old['image']);
    respond(['success' => true, 'message' => 'Đã xóa sản phẩm.']);
} catch (JsonException $exception) {
    respond(['success' => false, 'message' => 'JSON không hợp lệ.'], 400);
} catch (Throwable $exception) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    error_log($exception->getMessage());
    respond(['success' => false, 'message' => 'Không thể xử lý dữ liệu. Kiểm tra MySQL và file database.sql.'], 500);
}
