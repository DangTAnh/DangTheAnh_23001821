<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Quản lý giỏ hàng</title><link rel="stylesheet" href="assets/style.css"></head>
<body>
<div class="topbar">
  <h1><a href="product_list.php">Quản lý giỏ hàng</a></h1>
  <?php if (function_exists("currentUser") && currentUser()): ?>
  <p>Đăng nhập: <?= htmlspecialchars(currentUser()["username"]) ?> | <a href="logout.php">Đăng xuất</a></p>
  <?php endif; ?>
</div>
