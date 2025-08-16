<?php
session_start();
require_once '../includes/navbar.php';

$mensagem_erro = '';
if (isset($_SESSION['login_error'])) {
    $mensagem_erro = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Cadastro</title>
    <link rel="stylesheet" href="/educonect/css/global.css">
    <link rel="stylesheet" href="/educonect/css/navbar.css">
    <link rel="stylesheet" href="/educonect/css/forms.css">
    <link rel="stylesheet" href="/educonect/css/footer.css">
</head>
<body class="center-layout">
    <div class="form-container">
        <h2>Primeiro Acesso - Finalizar Cadastro</h2>
        <p style="text-align: center; margin-bottom: 20px;">Insira a matrícula e o código de cadastro que você recebeu da instituição.</p>
        
        <form action="validar_codigo.php" method="post">
            <?php
            if (!empty($mensagem_erro)) {
                echo "<div class='erro'>$mensagem_erro</div>";
            }
            ?>

            <label for="matricula">Sua Matrícula:</label>
            <input type="text" id="matricula" name="matricula" required>

            <label for="codigo">Seu Código de Cadastro:</label>
            <input type="text" id="codigo" name="codigo_cadastro" required>

            <button type="submit">Validar e Continuar</button>
        </form>
    </div>
    <?php require_once '../includes/footer.php'; ?>
</body>
</html>