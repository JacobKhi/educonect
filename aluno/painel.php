<?php
session_start();
require_once __DIR__ . '/../config.php';

// Garante que apenas alunos acedam a esta página
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 1) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

$nome_usuario = htmlspecialchars($_SESSION['usuario_nome']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Aluno</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container" style="max-width: 800px;">
        <div class="content-box">
            <h1>Painel do Aluno</h1>
            <p>Bem-vindo(a), <?php echo $nome_usuario; ?>!</p>

            <div class="menu-grid" style="grid-template-columns: 1fr;">
                <div class="menu-item">
                    <h3>Acesso Rápido</h3>
                    <a href="<?php echo BASE_URL; ?>/aluno/resumo.php">Ver Resumo Geral</a> 
                    <a href="<?php echo BASE_URL; ?>/aluno/minhas_atividades.php">Minhas Atividades</a>
                    <a href="<?php echo BASE_URL; ?>/aluno/minhas_notas.php">Minhas Notas</a>
                    <a href="<?php echo BASE_URL; ?>/aluno/minha_frequencia.php">Minha Frequência</a>
                    <a href="<?php echo BASE_URL; ?>/aluno/meus_materiais.php">Meus Materiais</a>
                </div>
            </div>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>