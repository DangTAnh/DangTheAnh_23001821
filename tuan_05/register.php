<?php
require_once __DIR__ . "/common/auth.php";
require_once __DIR__ . "/model/user.php";
require_once __DIR__ . "/view/header.php";

if (currentUser()) { header("Location: product_list.php"); exit; }

$error = $username = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";
    $error = validateRegister($username, $password);
    if ($error === null && getUserByUsername($username)) {
        $error = "Tên đăng nhập đã tồn tại.";
    }
    if ($error === null) {
        try {
            addUser($username, $password);
            header("Location: login.php?msg=" . urlencode("Đăng ký thành công, hãy đăng nhập."));
            exit;
        } catch (PDOException $e) {
            $error = "Lỗi đăng ký: " . $e->getMessage();
        }
    }
}
?>
<h2>Đăng ký</h2>
<?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<form method="post">
  Tên đăng nhập: <input name="username" value="<?= htmlspecialchars($username) ?>"><br>
  Mật khẩu: <input type="password" name="password"><br>
  <button>Đăng ký</button>
</form>
<p><a href="login.php">Đã có tài khoản? Đăng nhập</a></p>
<?php require_once __DIR__ . "/view/footer.php"; ?>
