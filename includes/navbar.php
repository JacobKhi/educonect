<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';

$dashboard_url = BASE_URL . '/aluno/painel.php';

if (isset($_SESSION['usuario_perfil_id'])) {
    switch ($_SESSION['usuario_perfil_id']) {
        case 2: // Se for Professor
            $dashboard_url = BASE_URL . '/professor/painel.php';
            break;
        case 4: // Se for Admin
            $dashboard_url = BASE_URL . '/admin/painel.php';
            break;

    }
}

?>
<div class="navbar">
    <a href="<?php echo BASE_URL; ?>/index.php">Início</a>

    <div class="navbar-right">
        <?php if (isset($_SESSION['usuario_id'])): ?>
            
            <a href="<?php echo $dashboard_url; ?>">Meu Painel</a>

            <a href="<?php echo BASE_URL; ?>/auth/login/logout.php">Sair</a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/auth/cadastro_aluno/finalizar_cadastro_aluno.php">Cadastro de Aluno</a>
            <a href="<?php echo BASE_URL; ?>/auth/cadastro_professor/finalizar_cadastro_professor.php">Cadastro de Professor</a>
            <a href="<?php echo BASE_URL; ?>/auth/login/login.php">Login</a>
        <?php endif; ?>
    </div>
</div>