<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/conexao.php';

// Bloco de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 2) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}
if (!isset($_GET['id'])) {
    header("Location: atividades.php");
    exit();
}

$id_atividade = $_GET['id'];
$id_professor = $_SESSION['usuario_id'];

// Lógica para ATUALIZAR
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $instrucoes = $_POST['instrucoes'];
    $data_entrega = $_POST['data_entrega'];

    $sql_update = "UPDATE atividades SET titulo = ?, instrucoes = ?, data_entrega = ? WHERE id = ?";
    $stmt_update = $conexao->prepare($sql_update);
    $stmt_update->bind_param("sssi", $titulo, $instrucoes, $data_entrega, $id_atividade);
    $stmt_update->execute();
    $stmt_update->close();

    header("Location: atividades.php"); // Adicionar mensagem de sucesso aqui depois
    exit();
}

// Busca os dados atuais da atividade
$sql_select = "SELECT * FROM atividades WHERE id = ?";
$stmt_select = $conexao->prepare($sql_select);
$stmt_select->bind_param("i", $id_atividade);
$stmt_select->execute();
$atividade = $stmt_select->get_result()->fetch_assoc();
$stmt_select->close();

if (!$atividade) {
    header("Location: atividades.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Atividade</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/forms.css">
</head>
<body class="center-layout">
    <div class="form-container">
        <h2>Editar Atividade</h2>
        <form method="post">
            <div class="form-group">
                <label for="titulo">Título da Atividade:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($atividade['titulo']); ?>" required>
            </div>
            <div class="form-group">
                <label for="instrucoes">Instruções:</label>
                <textarea id="instrucoes" name="instrucoes" rows="4"><?php echo htmlspecialchars($atividade['instrucoes']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="data_entrega">Data de Entrega:</label>
                <input type="date" id="data_entrega" name="data_entrega" value="<?php echo date('Y-m-d', strtotime($atividade['data_entrega'])); ?>">
            </div>
            <button type="submit" class="btn">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>