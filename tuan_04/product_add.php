<?php
require_once __DIR__ . "/model/product.php";
require_once __DIR__ . "/view/header.php";

$error = "";
$name = $price = $quantity = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"] ?? "";
    $price = $_POST["price"] ?? "";
    $quantity = $_POST["quantity"] ?? "";
    $error = validateProduct($name, $price, $quantity);
    if ($error === null) {
        try {
            addProduct($name, $price, $quantity);
            header("Location: product_list.php?msg=" . urlencode("Đã thêm sản phẩm."));
            exit;
        } catch (PDOException $e) {
            $error = "Lỗi thêm: " . $e->getMessage();
        }
    }
}
?>
<h2>Thêm sản phẩm</h2>
<?php if ($error): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<form method="post">
  Tên sản phẩm: <input name="name" value="<?= htmlspecialchars($name) ?>"><br>
  Giá: <input name="price" value="<?= htmlspecialchars($price) ?>"><br>
  Số lượng: <input name="quantity" value="<?= htmlspecialchars($quantity) ?>"><br>
  <button>Thêm</button>
</form>
<p><a href="product_list.php">← Danh sách</a></p>
<?php require_once __DIR__ . "/view/footer.php"; ?>
