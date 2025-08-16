<?php
session_start();
require_once __DIR__ . '/../../config.php';

session_destroy();
// Redireciona para o formulário de login no caminho correto
header("Location: " . BASE_URL . "/auth/login/login.php");
exit();
?>