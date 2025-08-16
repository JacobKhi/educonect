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
    <title>Pré-cadastro de Aluno</title>
    <link rel="stylesheet" href="/educonect/css/global.css">
    <link rel="stylesheet" href="/educonect/css/navbar.css">
    <link rel="stylesheet" href="/educonect/css/forms.css">
    <link rel="stylesheet" href="/educonect/css/footer.css">
</head>
<body class="center-layout">
    <div class="form-container">
        <h2>Pré-cadastrar Novo Aluno</h2>
        <form action="admin_processa_aluno.php" method="post">
            <label for="nome_aluno">Nome Completo do Aluno:</label>
            <input type="text" id="nome_aluno" name="nome_completo_aluno" required>

            <hr style="margin: 20px 0;">

            <label for="nome_responsavel">Nome do Responsável (Opcional):</label>
            <input type="text" id="nome_responsavel" name="nome_completo_responsavel">
            <small>Se preenchido, um código de cadastro também será gerado para o responsável.</small>

            <button type="submit" style="margin-top: 20px;">Gerar Convites</button>
        </form>
    </div>
    <?php require_once '../includes/footer.php'; ?>
</body>
</html>