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
    header("Location: alocar_professores.php");
    exit();
}

$id_alocacao = $_GET['id'];

// Lógica para EXCLUIR a alocação
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql_delete = "DELETE FROM professores_turmas_disciplinas WHERE id = ?";
    $stmt_delete = $conexao->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_alocacao);
    $stmt_delete->execute();
    $stmt_delete->close();
    header("Location: alocar_professores.php");
    exit();
}

// Busca os dados da alocação para a mensagem de confirmação
$sql_select = "
    SELECT u.nome AS nome_professor, t.nome AS nome_turma, d.nome AS nome_disciplina
    FROM professores_turmas_disciplinas ptd
    JOIN usuarios u ON ptd.id_professor_usuario = u.id
    JOIN turmas t ON ptd.id_turma = t.id
    JOIN disciplinas d ON ptd.id_disciplina = d.id
    WHERE ptd.id = ?
";
$stmt_select = $conexao->prepare($sql_select);
$stmt_select->bind_param("i", $id_alocacao);
$stmt_select->execute();
$resultado = $stmt_select->get_result();
$alocacao = $resultado->fetch_assoc();
$stmt_select->close();

if (!$alocacao) {
    header("Location: alocar_professores.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Exclusão de Alocação</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/forms.css">
</head>
<body class="center-layout">
    <div class="form-container" style="text-align: center;">
        <h2>Confirmar Exclusão</h2>
        <p>Você tem certeza que deseja remover a seguinte alocação?</p>
        <div style="padding: 15px; background-color: #f3f4f6; border-radius: 8px; margin: 15px 0;">
            <strong>Professor:</strong> <?php echo htmlspecialchars($alocacao['nome_professor']); ?><br>
            <strong>Turma:</strong> <?php echo htmlspecialchars($alocacao['nome_turma']); ?><br>
            <strong>Disciplina:</strong> <?php echo htmlspecialchars($alocacao['nome_disciplina']); ?>
        </div>
        <form method="post">
            <button type="submit" class="btn" style="background-color: #ef4444;">Sim, Excluir Alocação</button>
            <a href="alocar_professores.php" class="btn" style="background-color: #ccc; display: block; margin-top: 10px; text-decoration: none;">Cancelar</a>
        </form>
    </div>
</body>
</html>