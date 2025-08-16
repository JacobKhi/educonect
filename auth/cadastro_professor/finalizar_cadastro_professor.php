<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/navbar.php';

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
    <title>Finalizar Cadastro de Professor</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/forms.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
</head>
<body class="center-layout">
    <div class="form-container">
        <h2>Primeiro Acesso - Professor</h2>
        <p style="text-align: center; margin-bottom: 20px;">Insira a matrícula e o código de cadastro que você recebeu.</p>
        
        <form action="validar_codigo_professor.php" method="post">
             <?php
            if (!empty($mensagem_erro)) {
                echo "<div class='erro'>$mensagem_erro</div>";
            }
            ?>
            <label for="matricula">Sua Matrícula de Professor:</label>
            <input type="text" id="matricula" name="matricula_professor" required>

            <label for="codigo">Seu Código de Cadastro:</label>
            <input type="text" id="codigo" name="codigo_cadastro" required>

            <button type="submit">Validar e Continuar</button>
        </form>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>