<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';

// Define a URL do painel com base no perfil do utilizador
$dashboard_url = BASE_URL . '/aluno/painel.php'; // Padrão para aluno
if (isset($_SESSION['usuario_perfil_id'])) {
    switch ($_SESSION['usuario_perfil_id']) {
        case 2: // Professor
            $dashboard_url = BASE_URL . '/professor/painel.php';
            break;
        case 3: // Responsável
            $dashboard_url = BASE_URL . '/responsavel/painel.php';
            break;
        case 4: // Admin
            $dashboard_url = BASE_URL . '/admin/painel.php';
            break;
    }
}

$is_home_page = basename($_SERVER['SCRIPT_NAME']) == 'index.php';
?>
<div class="navbar">
    <a href="<?php echo BASE_URL; ?>/index.php" class="logo">EduConect</a>

    <div class="navbar-right">
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <a href="<?php echo $dashboard_url; ?>">Meu Painel</a>
            <a href="<?php echo BASE_URL; ?>/calendario.php">Calendário</a> 
            <a href="<?php echo BASE_URL; ?>/comum/perfil.php">Meu Perfil</a>
            <a href="<?php echo BASE_URL; ?>/auth/login/logout.php">Sair</a>

        <?php else: ?>
            <?php if ($is_home_page): ?>
                <a href="<?php echo BASE_URL; ?>/auth/login/login.php" class="btn-login">Login</a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/auth/cadastro_aluno/finalizar_cadastro_aluno.php">Cadastro de Aluno</a>
                <a href="<?php echo BASE_URL; ?>/auth/cadastro_professor/finalizar_cadastro_professor.php">Cadastro de Professor</a>
                <a href="<?php echo BASE_URL; ?>/auth/cadastro_responsavel/finalizar_cadastro_responsavel.php">Cadastro de Responsável</a>
                <a href="<?php echo BASE_URL; ?>/auth/login/login.php">Login</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>