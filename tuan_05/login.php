<?php
require_once __DIR__ . "/common/auth.php";
require_once __DIR__ . "/model/user.php";
require_once __DIR__ . "/view/header.php";

if (currentUser()) { header("Location: product_list.php"); exit; }

$error = $username = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";
    $user = $username !== "" && $password !== "" ? getUserByUsername($username) : null;
    if (!$user || !password_verify($password, $user["password"])) {
        $error = "Sai tên đăng nhập hoặc mật khẩu.";
    } else {
        session_regenerate_id(true);
        $_SESSION["user"] = ["id" => $user["id"], "username" => $user["username"]];
        header("Location: product_list.php");
        exit;
    }
}
?>
<h2>Đăng nhập</h2>
<?php if (isset($_GET["msg"])): ?><p><?= htmlspecialchars($_GET["msg"]) ?></p><?php endif; ?>
<?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
<form method="post">
  Tên đăng nhập: <input name="username" value="<?= htmlspecialchars($username) ?>"><br>
  Mật khẩu: <input type="password" name="password"><br>
  <button>Đăng nhập</button>
</form>
<p><a href="register.php">Chưa có tài khoản? Đăng ký</a></p>
<?php require_once __DIR__ . "/view/footer.php"; ?>
