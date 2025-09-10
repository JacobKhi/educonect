<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/includes/navbar.php';

// Bloco de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Gestor</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
</head>
<body class="center-layout">
    <div class="container">
        <div class="content-box">
            <h1>Painel do Gestor</h1>
            <p>Bem-vindo(a), <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</p>

            <div class="menu-grid">
                <div class="menu-item">
                    <h3>Gestão de Convites</h3>
                    <a href="cadastrar/aluno.php">Pré-cadastrar Aluno</a><br>
                    <a href="cadastrar/professor.php">Pré-cadastrar Professor</a><br>
                    <a href="cadastrar/responsavel.php">Pré-cadastrar Responsável</a><br>
                    <a href="ver_pendentes.php">Ver Convites Pendentes</a>
                </div>
                <div class="menu-item">
                    <h3>Gestão Acadêmica</h3>
                    <a href="gerenciar/disciplinas.php">Gerenciar Disciplinas</a><br>
                    <a href="gerenciar/turmas.php">Gerenciar Turmas</a><br>
                    <a href="gerenciar/calendario.php">Gerenciar Calendário</a><br>
                    <a href="gerenciar/alocar_professores.php">Alocar Professores</a><br>
                    <a href="gerenciar/matricular_alunos.php">Matricular Alunos</a><br>
                    <a href="gerenciar/responsaveis.php">Gerenciar Responsáveis</a><br>
                    <a href="gerenciar/usuarios.php">Ver Usuários Ativos</a><br>
                </div>
            </div>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>