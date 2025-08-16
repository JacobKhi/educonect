<?php
session_start();
require_once __DIR__ . '/config.php'; 

// Se o usuário não estiver logado, redireciona para o caminho correto do login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . BASE_URL . "/auth/login/login.php");
    exit();
}

require_once BASE_PATH . '/includes/navbar.php';
$nome_usuario = htmlspecialchars($_SESSION['usuario_nome']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Principal</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
</head>
<body>
    <div class="container">
        <div class="content-box">
            <h1>Bem-vindo(a), <?php echo $nome_usuario; ?>!</h1>
            <p>Você está logado no sistema EduConect.</p>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>