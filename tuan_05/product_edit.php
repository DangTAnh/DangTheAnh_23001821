<?php
require_once __DIR__ . "/common/auth.php";
requireLogin();
require_once __DIR__ . "/model/product.php";

try {
    $p = getProductById($_GET["id"] ?? "");
} catch (PDOException $e) {
    die("Lỗi đọc sản phẩm: " . htmlspecialchars($e->getMessage()));
}
if (!$p) die("Sản phẩm không tồn tại. <a href='product_list.php'>← Danh sách</a>");

require_once __DIR__ . "/view/header.php";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"] ?? "";
    $price = $_POST["price"] ?? "";
    $quantity = $_POST["quantity"] ?? "";
    $error = validateProduct($name, $price, $quantity);
    if ($error === null) {
        try {
            updateProduct($p["id"], $name, $price, $quantity);
            header("Location: product_list.php?msg=" . urlencode("Đã cập nhật sản phẩm."));
            exit;
        } catch (PDOException $e) {
            $error = "Lỗi cập nhật: " . $e->getMessage();
        }
    }
    $p = ["id" => $p["id"], "name" => $name, "price" => $price, "quantity" => $quantity];
}
?>
<h2>Sửa sản phẩm #<?= $p["id"] ?></h2>
<?php if ($error): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<form method="post">
  Tên sản phẩm: <input name="name" value="<?= htmlspecialchars($p["name"]) ?>"><br>
  Giá: <input name="price" value="<?= htmlspecialchars($p["price"]) ?>"><br>
  Số lượng: <input name="quantity" value="<?= htmlspecialchars($p["quantity"]) ?>"><br>
  <button>Lưu</button>
</form>
<p><a href="product_list.php">← Danh sách</a></p>
<?php require_once __DIR__ . "/view/footer.php"; ?>
