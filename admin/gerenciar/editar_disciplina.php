<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

// Bloco de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

// Verifica se o ID foi passado
if (!isset($_GET['id'])) {
    header("Location: disciplinas.php");
    exit();
}

$id_disciplina = $_GET['id'];

// Lógica para ATUALIZAR a disciplina
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novo_nome = $_POST['nome_disciplina'];
    if (!empty($novo_nome)) {
        $sql_update = "UPDATE disciplinas SET nome = ? WHERE id = ?";
        $stmt_update = $conexao->prepare($sql_update);
        $stmt_update->bind_param("si", $novo_nome, $id_disciplina);
        $stmt_update->execute();
        $stmt_update->close();
        header("Location: disciplinas.php");
        exit();
    }
}

// Busca os dados atuais da disciplina para preencher o formulário
$sql_select = "SELECT nome FROM disciplinas WHERE id = ?";
$stmt_select = $conexao->prepare($sql_select);
$stmt_select->bind_param("i", $id_disciplina);
$stmt_select->execute();
$resultado = $stmt_select->get_result();
$disciplina = $resultado->fetch_assoc();
$stmt_select->close();

if (!$disciplina) {
    header("Location: disciplinas.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Disciplina</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/forms.css">
</head>
<body class="center-layout">
    <div class="form-container">
        <h2>Editar Disciplina</h2>
        <form method="post">
            <label for="nome">Nome da Disciplina:</label>
            <input type="text" id="nome" name="nome_disciplina" value="<?php echo htmlspecialchars($disciplina['nome']); ?>" required>
            <button type="submit" class="btn">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>