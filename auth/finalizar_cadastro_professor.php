<?php require_once '../includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Cadastro de Professor</title>
    <link rel="stylesheet" href="/educonect/css/global.css">
    <link rel="stylesheet" href="/educonect/css/navbar.css">
    <link rel="stylesheet" href="/educonect/css/forms.css">
    <link rel="stylesheet" href="/educonect/css/footer.css">
</head>
<body class="center-layout">
    <div class="form-container">
        <h2>Primeiro Acesso - Professor</h2>
        <p style="text-align: center; margin-bottom: 20px;">Insira a matrícula e o código de cadastro que você recebeu da instituição.</p>
        <form action="validar_codigo_professor.php" method="post">
            <label for="matricula">Sua Matrícula de Professor:</label>
            <input type="text" id="matricula" name="matricula_professor" required>

            <label for="codigo">Seu Código de Cadastro:</label>
            <input type="text" id="codigo" name="codigo_cadastro" required>

            <button type="submit">Validar e Continuar</button>
        </form>
    </div>
    <?php require_once '../includes/footer.php'; ?>
</body>
</html>