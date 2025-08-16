<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Carrega o config.php para ter acesso ao BASE_URL
// __DIR__ se refere à pasta atual (includes), então ../ vai para a raiz do projeto
require_once __DIR__ . '/../config.php';
?>
<div class="navbar">
    <a href="<?php echo BASE_URL; ?>/index.php">Início</a>

    <div class="navbar-right">
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <a href="<?php echo BASE_URL; ?>/dashboard.php">Meu Painel</a>
            
            <?php if ($_SESSION['usuario_perfil_id'] == 4): // Apenas se for gestor ?>
                <a href="<?php echo BASE_URL; ?>/admin/painel.php">Painel Admin</a>
            <?php endif; ?>

            <a href="<?php echo BASE_URL; ?>/auth/login/logout.php">Sair</a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/auth/cadastro_aluno/finalizar_cadastro_aluno.php">Cadastro de Aluno</a>
            <a href="<?php echo BASE_URL; ?>/auth/cadastro_professor/finalizar_cadastro_professor.php">Cadastro de Professor</a>
            <a href="<?php echo BASE_URL; ?>/auth/login/login.php">Login</a>
        <?php endif; ?>
    </div>
</div>