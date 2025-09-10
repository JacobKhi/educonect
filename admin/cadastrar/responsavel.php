<?php
session_start();
require_once __DIR__ . '/../../config.php';

// Bloco de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

require_once BASE_PATH . '/includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pré-cadastro de Responsável</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/forms.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
</head>
<body class="center-layout">
    <div class="form-container">
        <h2>Pré-cadastrar Novo Responsável</h2>
        <form action="../processar/responsavel.php" method="post">
            <label for="nome_responsavel">Nome Completo do Responsável:</label>
            <input type="text" id="nome_responsavel" name="nome_completo_responsavel" required>
            <button type="submit">Gerar Convite</button>
        </form>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>