<?php
require_once __DIR__ . "/model/product.php";

try {
    $p = getProductById($_GET["id"] ?? "");
} catch (PDOException $e) {
    die("Lỗi đọc sản phẩm: " . htmlspecialchars($e->getMessage()));
}
if (!$p) die("Sản phẩm không tồn tại. <a href='product_list.php'>← Danh sách</a>");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        deleteProduct($p["id"]);
        header("Location: product_list.php?msg=" . urlencode("Đã xóa sản phẩm."));
        exit;
    } catch (PDOException $e) {
        die("Lỗi xóa: " . htmlspecialchars($e->getMessage()));
    }
}

require_once __DIR__ . "/view/header.php";
?>
<h2>Xóa sản phẩm #<?= $p["id"] ?> - <?= htmlspecialchars($p["name"]) ?>?</h2>
<form method="post"><button>Xác nhận xóa</button></form>
<p><a href="product_list.php">← Danh sách</a></p>
<?php require_once __DIR__ . "/view/footer.php"; ?>
