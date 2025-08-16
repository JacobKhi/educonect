<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    // Caminho corrigido para a página de login
    header("Location: auth/login.php");
    exit();
}

// Inclui a navbar depois de verificar a sessão
require_once 'includes/navbar.php';

$nome_usuario = htmlspecialchars($_SESSION['usuario_nome']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Usuário</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
    </style>
</head>
<body>
    <h1>Bem-vindo(a), <?php echo $nome_usuario; ?>!</h1>
    <p>Você está logado no sistema EduConect.</p>
</body>
</html>