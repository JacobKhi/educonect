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
    <title>Login - EduConect</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/educonect/css/global.css">
    <link rel="stylesheet" href="/educonect/css/navbar.css">
    <link rel="stylesheet" href="/educonect/css/forms.css">
    <link rel="stylesheet" href="/educonect/css/footer.css"> 
</head>
<body class="center-layout"> <div class="form-container">
        <form action="processa_login.php" method="post">
            <h2>Acessar o Sistema</h2>

            <?php
            if (!empty($mensagem_erro)) {
                echo "<div class='erro'>$mensagem_erro</div>";
            }
            ?>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
            <button type="submit">Entrar</button>
        </form>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>