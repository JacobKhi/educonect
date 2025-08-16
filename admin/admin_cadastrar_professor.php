<?php
session_start();
require_once '../includes/navbar.php';

// Garantir que apenas gestores acessem
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pré-cadastro de Professor</title>
    <link rel="stylesheet" href="/educonect/css/global.css">
    <link rel="stylesheet" href="/educonect/css/navbar.css">
    <link rel="stylesheet" href="/educonect/css/forms.css">
    <link rel="stylesheet" href="/educonect/css/footer.css">
</head>
<body class="center-layout">
    <div class="form-container">
        <h2>Pré-cadastrar Novo Professor</h2>
        <form action="admin_processa_professor.php" method="post">
            <label for="nome">Nome Completo do Professor:</label>
            <input type="text" id="nome" name="nome_completo" required>
            <button type="submit">Gerar Matrícula e Código</button>
        </form>
    </div>
    <?php require_once '../includes/footer.php'; ?>
</body>
</html>