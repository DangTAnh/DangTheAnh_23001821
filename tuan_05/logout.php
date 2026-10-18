<?php
require_once __DIR__ . "/common/auth.php";
session_destroy();
header("Location: login.php");
