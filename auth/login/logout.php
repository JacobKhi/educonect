<?php
session_start();
require_once __DIR__ . '/../../config.php';

session_destroy();
header("Location: " . BASE_URL . "/auth/login/login.php");
exit();
?>