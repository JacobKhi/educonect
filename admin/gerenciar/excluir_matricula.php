<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

// Bloco de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

if (!isset($_GET['id_aluno']) || !isset($_GET['id_turma'])) {
    header("Location: matricular_alunos.php");
    exit();
}

$id_aluno = $_GET['id_aluno'];
$id_turma = $_GET['id_turma'];

// Lógica para EXCLUIR a matrícula
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id_aluno_hidden']) && isset($_POST['id_turma_hidden'])) {
        $id_aluno_post = $_POST['id_aluno_hidden'];
        $id_turma_post = $_POST['id_turma_hidden'];
        
        $sql_delete = "DELETE FROM alunos_turmas WHERE id_aluno_usuario = ? AND id_turma = ?";
        $stmt_delete = $conexao->prepare($sql_delete);
        $stmt_delete->bind_param("ii", $id_aluno_post, $id_turma_post);
        $stmt_delete->execute();
        $stmt_delete->close();
    }
    header("Location: matricular_alunos.php");
    exit();
}

// Busca os dados para a mensagem de confirmação
$sql_aluno = "SELECT nome FROM usuarios WHERE id = ?";
$stmt_aluno = $conexao->prepare($sql_aluno);
$stmt_aluno->bind_param("i", $id_aluno);
$stmt_aluno->execute();
$aluno = $stmt_aluno->get_result()->fetch_assoc();
$stmt_aluno->close();

$sql_turma = "SELECT nome FROM turmas WHERE id = ?";
$stmt_turma = $conexao->prepare($sql_turma);
$stmt_turma->bind_param("i", $id_turma);
$stmt_turma->execute();
$turma = $stmt_turma->get_result()->fetch_assoc();
$stmt_turma->close();

if (!$aluno || !$turma) {
    header("Location: matricular_alunos.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Exclusão de Matrícula</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/forms.css">
</head>
<body class="center-layout">
    <div class="form-container" style="text-align: center;">
        <h2>Confirmar Exclusão</h2>
        <p>Você tem certeza que deseja remover a matrícula do aluno:</p>
        <h3 style="color: #1E3A8A;"><?php echo htmlspecialchars($aluno['nome']); ?></h3>
        <p>da turma:</p>
        <h3 style="color: #1E3A8A;"><?php echo htmlspecialchars($turma['nome']); ?></h3>
        
        <form method="post">
            <input type="hidden" name="id_aluno_hidden" value="<?php echo $id_aluno; ?>">
            <input type="hidden" name="id_turma_hidden" value="<?php echo $id_turma; ?>">
            <button type="submit" class="btn" style="background-color: #ef4444;">Sim, Excluir Matrícula</button>
            <a href="matricular_alunos.php" class="btn" style="background-color: #ccc; display: block; margin-top: 10px; text-decoration: none;">Cancelar</a>
        </form>
    </div>
</body>
</html>