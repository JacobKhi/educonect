<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="navbar">
    <a href="/educonect/index.php">Início</a>
    <a href="/educonect/auth/finalizar_cadastro.php">Finalizar Cadastro</a>
    <a href="/educonect/admin/admin_cadastrar_aluno.php">Admin: Cadastrar Aluno</a>
    <a href="/educonect/admin/painel.php">Painel Admin</a>
    
    <div class="navbar-right">
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <a href="/educonect/dashboard.php">Meu Painel</a>
            <a href="/educonect/auth/logout.php">Sair</a>
        <?php else: ?>
            <a href="/educonect/auth/login.php">Login</a>
        <?php endif; ?>
    </div>
</div>