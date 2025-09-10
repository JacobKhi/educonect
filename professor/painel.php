<?php
session_start();

require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/conexao.php';

// Garante que apenas professores acessem esta página
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 2) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

$nome_usuario = htmlspecialchars($_SESSION['usuario_nome']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Professor</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container" style="max-width: 800px;">
        <div class="content-box">
            <h1>Painel do Professor</h1>
            <p>Bem-vindo(a), Prof. <?php echo $nome_usuario; ?>!</p>

            <div class="menu-grid" style="grid-template-columns: 1fr;">
                <div class="menu-item">
                    <h3>Ações Principais</h3>
                    <a href="<?php echo BASE_URL; ?>/professor/atividades.php">Gerenciar Atividades</a>
                    <a href="<?php echo BASE_URL; ?>/professor/lancar_notas.php">Lançar Notas</a>
                    <a href="<?php echo BASE_URL; ?>/professor/registrar_frequencia.php">Registrar Frequência</a>
                    <a href="<?php echo BASE_URL; ?>/professor/materiais.php">Gerenciar Materiais</a>
                </div>
            </div>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>