<?php
require_once __DIR__ . "/../common/dbConnect.php";

function getAllProducts() {
    return dbConnect()->query("SELECT * FROM products ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
}

function getProductById($id) {
    if (!ctype_digit((string)$id)) return null;
    $st = dbConnect()->prepare("SELECT * FROM products WHERE id = ?");
    $st->execute([$id]);
    return $st->fetch(PDO::FETCH_ASSOC) ?: null;
}

function validateProduct($name, $price, $quantity) {
    if (trim($name) === "") return "Tên không được rỗng.";
    if (!is_numeric($price) || $price <= 0) return "Giá phải > 0.";
    if (!ctype_digit((string)$quantity)) return "Số lượng phải là số nguyên >= 0.";
    return null;
}

function addProduct($name, $price, $quantity) {
    $st = dbConnect()->prepare("INSERT INTO products (name, price, quantity) VALUES (?, ?, ?)");
    $st->execute([trim($name), $price, $quantity]);
}

function updateProduct($id, $name, $price, $quantity) {
    $st = dbConnect()->prepare("UPDATE products SET name = ?, price = ?, quantity = ? WHERE id = ?");
    $st->execute([trim($name), $price, $quantity, $id]);
    return $st->rowCount();
}

function deleteProduct($id) {
    $st = dbConnect()->prepare("DELETE FROM products WHERE id = ?");
    $st->execute([$id]);
    return $st->rowCount();
}
