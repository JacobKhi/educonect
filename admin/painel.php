<?php
session_start();
require_once '../includes/navbar.php';

// --- BLOCO DE SEGURANÇA ---
// Primeiro, verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: ../auth/login.php");
    exit();
}

// Segundo, verifica se o perfil do usuário é de GESTOR (id_perfil = 4)
if ($_SESSION['usuario_perfil_id'] != 4) {
    // Se não for gestor, redireciona para o painel principal (ou uma página de acesso negado)
    header("Location: ../dashboard.php");
    exit();
}
// --- FIM DO BLOCO DE SEGURANÇA ---

// Se o script chegou até aqui, significa que o usuário é um gestor logado.
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Gestor</title>
    <link rel="stylesheet" href="/educonect/css/global.css">
    <link rel="stylesheet" href="/educonect/css/navbar.css">
    <style>
        .container { max-width: 900px; margin: 20px auto; padding: 20px; background-color: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .menu-item { padding: 20px; border: 1px solid #ddd; border-radius: 5px; text-align: center; }
        .menu-item h3 { margin-top: 0; }
        .menu-item a { text-decoration: none; color: #007bff; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Painel do Gestor</h1>
        <p>Bem-vindo(a), <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</p>

        <div class="menu-grid">
            <div class="menu-item">
                <h3>Gestão de Convites</h3>
                <a href="admin_cadastrar_aluno.php">Pré-cadastrar Aluno</a><br>
                <a href="admin_cadastrar_professor.php">Pré-cadastrar Professor</a><br>
                <a href="ver_pendentes.php">Ver Convites Pendentes</a>
            </div>
            <div class="menu-item">
                <h3>Gestão Acadêmica</h3>
                <a href="gerenciar_disciplinas.php">Gerenciar Disciplinas</a><br>
                <a href="gerenciar_turmas.php">Gerenciar Turmas</a><br>
                <a href="#">Alocar Professores (a fazer)</a><br>
                <a href="#">Matricular Alunos (a fazer)</a>
            </div>
        </div>
    </div>
</body>
</html>
