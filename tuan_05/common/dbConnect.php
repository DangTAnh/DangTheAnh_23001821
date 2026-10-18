<?php
// ponytail: single PDO instance per request via static, enough for CRUD demo.
function dbConnect() {
    static $pdo = null;
    if ($pdo !== null) return $pdo;
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=shopping_cart;charset=utf8mb4", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Kết nối thất bại: " . htmlspecialchars($e->getMessage()));
    }
    return $pdo;
}
