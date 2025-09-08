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

// Lógica para ATUALIZAR a turma
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novo_nome = $_POST['nome_turma'];
    $novo_ano = $_POST['ano_letivo'];
    if (!empty($novo_nome) && !empty($novo_ano)) {
        $sql_update = "UPDATE turmas SET nome = ?, ano_letivo = ? WHERE id = ?";
        $stmt_update = $conexao->prepare($sql_update);
        $stmt_update->bind_param("sii", $novo_nome, $novo_ano, $id_turma);
        $stmt_update->execute();
        $stmt_update->close();
        header("Location: turmas.php");
        exit();
    }
}

// Busca os dados atuais da turma
$sql_select = "SELECT nome, ano_letivo FROM turmas WHERE id = ?";
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
    <title>Editar Turma</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/forms.css">
</head>
<body class="center-layout">
    <div class="form-container">
        <h2>Editar Turma</h2>
        <form method="post">
            <div class="form-group">
                <label for="nome_turma">Nome da Turma:</label>
                <input type="text" id="nome_turma" name="nome_turma" value="<?php echo htmlspecialchars($turma['nome']); ?>" required>
            </div>
            <div class="form-group">
                <label for="ano_letivo">Ano Letivo:</label>
                <input type="number" id="ano_letivo" name="ano_letivo" value="<?php echo htmlspecialchars($turma['ano_letivo']); ?>" required>
            </div>
            <button type="submit" class="btn">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>