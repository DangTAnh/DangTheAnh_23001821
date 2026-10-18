<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function requireLogin() {
    if (empty($_SESSION["user"])) {
        header("Location: login.php");
        exit;
    }
}

function currentUser() {
    return $_SESSION["user"] ?? null;
}
