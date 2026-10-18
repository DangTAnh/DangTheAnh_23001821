<?php
require_once __DIR__ . "/../common/dbConnect.php";

function getUserByUsername($username) {
    $st = dbConnect()->prepare("SELECT * FROM users WHERE username = ?");
    $st->execute([$username]);
    return $st->fetch(PDO::FETCH_ASSOC) ?: null;
}

function addUser($username, $password) {
    $st = dbConnect()->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $st->execute([trim($username), password_hash($password, PASSWORD_DEFAULT)]);
}

function validateRegister($username, $password) {
    if (trim($username) === "") return "Tên đăng nhập không được rỗng.";
    if ($password === "") return "Mật khẩu không được rỗng.";
    return null;
}
