<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

// Bloco de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: turmas.php");
    exit();
}

$id_turma = $_GET['id'];

// Lógica para EXCLUIR a turma
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql_delete = "DELETE FROM turmas WHERE id = ?";
    $stmt_delete = $conexao->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_turma);
    $stmt_delete->execute();
    $stmt_delete->close();
    header("Location: turmas.php");
    exit();
}

// Busca o nome da turma para a mensagem de confirmação
$sql_select = "SELECT nome FROM turmas WHERE id = ?";
$stmt_select = $conexao->prepare($sql_select);
$stmt_select->bind_param("i", $id_turma);
$stmt_select->execute();
$resultado = $stmt_select->get_result();
$turma = $resultado->fetch_assoc();
$stmt_select->close();

if (!$turma) {
    header("Location: turmas.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Exclusão</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/forms.css">
</head>
<body class="center-layout">
    <div class="form-container" style="text-align: center;">
        <h2>Confirmar Exclusão</h2>
        <p>Você tem certeza que deseja excluir a turma?</p>
        <h3 style="color: #ef4444;"><?php echo htmlspecialchars($turma['nome']); ?></h3>
        <p><small>Atenção: Todas as alocações de professores e matrículas de alunos nesta turma também serão perdidas.</small></p>
        <form method="post">
            <button type="submit" class="btn" style="background-color: #ef4444;">Sim, Excluir</button>
            <a href="turmas.php" class="btn" style="background-color: #ccc; display: block; margin-top: 10px; text-decoration: none;">Cancelar</a>
        </form>
    </div>
</body>
</html>