<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/includes/navbar.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pré-cadastro de Aluno</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/forms.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
</head>
<body class="center-layout">
    <div class="form-container">
        <h2>Pré-cadastrar Novo Aluno</h2>
        <form action="../processar/aluno.php" method="post">
            <label for="nome_aluno">Nome Completo do Aluno:</label>
            <input type="text" id="nome_aluno" name="nome_completo_aluno" required>
            <label for="nome_responsavel">Nome do Responsável (Opcional):</label>
            <input type="text" id="nome_responsavel" name="nome_completo_responsavel">
            <button type="submit">Gerar Convites</button>
        </form>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>