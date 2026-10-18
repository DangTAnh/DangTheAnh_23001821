<?php
require_once __DIR__ . "/common/auth.php";
requireLogin();
require_once __DIR__ . "/model/product.php";
require_once __DIR__ . "/view/header.php";

try {
    $products = getAllProducts();
} catch (PDOException $e) {
    die("Lỗi đọc sản phẩm: " . htmlspecialchars($e->getMessage()));
}
?>
<p><a href="product_add.php">+ Thêm sản phẩm</a></p>
<?php if (isset($_GET["msg"])): ?><p><?= htmlspecialchars($_GET["msg"]) ?></p><?php endif; ?>
<?php if (empty($products)): ?>
<p>Chưa có sản phẩm.</p>
<?php else: ?>
<table border="1" cellpadding="6">
<tr><th>ID</th><th>Tên sản phẩm</th><th>Giá</th><th>Số lượng</th><th>Chức năng</th></tr>
<?php foreach ($products as $p): ?>
<tr>
  <td><?= $p["id"] ?></td>
  <td><?= htmlspecialchars($p["name"]) ?></td>
  <td><?= htmlspecialchars($p["price"]) ?></td>
  <td><?= htmlspecialchars($p["quantity"]) ?></td>
  <td><a href="product_edit.php?id=<?= $p["id"] ?>">Sửa</a> | <a href="product_delete.php?id=<?= $p["id"] ?>">Xóa</a></td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
<?php require_once __DIR__ . "/view/footer.php"; ?>
