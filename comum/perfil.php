<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/conexao.php';

// Redireciona se não houver ninguém logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$id_perfil = $_SESSION['usuario_perfil_id'];
$dados_usuario = [];

// 1. Busca os dados básicos da tabela 'usuarios'
$stmt_base = $conexao->prepare("SELECT nome, email, data_criacao FROM usuarios WHERE id = ?");
$stmt_base->bind_param("i", $id_usuario);
$stmt_base->execute();
$dados_usuario = $stmt_base->get_result()->fetch_assoc();
$stmt_base->close();

// 2. Busca dados adicionais dependendo do perfil
switch ($id_perfil) {
    case 1: // Aluno
        $stmt_aluno = $conexao->prepare("SELECT matricula, data_nascimento FROM dados_alunos WHERE id_usuario = ?");
        $stmt_aluno->bind_param("i", $id_usuario);
        $stmt_aluno->execute();
        $dados_adicionais = $stmt_aluno->get_result()->fetch_assoc();
        $dados_usuario = array_merge($dados_usuario, $dados_adicionais);
        $stmt_aluno->close();
        break;
    case 2: // Professor
        $stmt_prof = $conexao->prepare("SELECT especializacao FROM dados_professores WHERE id_usuario = ?");
        $stmt_prof->bind_param("i", $id_usuario);
        $stmt_prof->execute();
        $dados_adicionais = $stmt_prof->get_result()->fetch_assoc();
        $dados_usuario = array_merge($dados_usuario, $dados_adicionais);
        $stmt_prof->close();
        break;
    // Perfis 3 (Responsável) e 4 (Gestor) não têm tabelas de dados adicionais por enquanto
}

$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
    <style>
        .profile-info { text-align: left; max-width: 600px; margin: 0 auto; }
        .profile-info p { border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
        .profile-info strong { color: #1E3A8A; }
    </style>
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <div class="content-box">
            <h1>Meu Perfil</h1>
            <hr>
            <div class="profile-info">
                <p><strong>Nome Completo:</strong> <?php echo htmlspecialchars($dados_usuario['nome']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($dados_usuario['email']); ?></p>

                <?php if ($id_perfil == 1): // Aluno ?>
                    <p><strong>Matrícula:</strong> <?php echo htmlspecialchars($dados_usuario['matricula']); ?></p>
                    <p><strong>Data de Nascimento:</strong> <?php echo date('d/m/Y', strtotime($dados_usuario['data_nascimento'])); ?></p>
                <?php endif; ?>

                <?php if ($id_perfil == 2): // Professor ?>
                    <p><strong>Especialização Principal:</strong> <?php echo htmlspecialchars($dados_usuario['especializacao']); ?></p>
                <?php endif; ?>

                <p><strong>Membro desde:</strong> <?php echo date('d/m/Y', strtotime($dados_usuario['data_criacao'])); ?></p>
            </div>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>