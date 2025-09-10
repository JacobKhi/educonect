<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 2 || !isset($_GET['id'])) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

$id_atividade = $_GET['id'];

// Lógica para EXCLUIR
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Apagar as notas associadas primeiro para evitar erros
    $sql_delete_notas = "DELETE FROM notas WHERE id_atividade = ?";
    $stmt_delete_notas = $conexao->prepare($sql_delete_notas);
    $stmt_delete_notas->bind_param("i", $id_atividade);
    $stmt_delete_notas->execute();
    $stmt_delete_notas->close();
    
    // Apagar a atividade
    $sql_delete_ativ = "DELETE FROM atividades WHERE id = ?";
    $stmt_delete_ativ = $conexao->prepare($sql_delete_ativ);
    $stmt_delete_ativ->bind_param("i", $id_atividade);
    $stmt_delete_ativ->execute();
    $stmt_delete_ativ->close();

    header("Location: atividades.php");
    exit();
}

// Busca o título da atividade para a mensagem de confirmação
$sql_select = "SELECT titulo FROM atividades WHERE id = ?";
$stmt_select = $conexao->prepare($sql_select);
$stmt_select->bind_param("i", $id_atividade);
$stmt_select->execute();
$atividade = $stmt_select->get_result()->fetch_assoc();

if (!$atividade) {
    header("Location: atividades.php");
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
        <p>Tem a certeza que deseja excluir a atividade?</p>
        <h3 style="color: #ef4444;"><?php echo htmlspecialchars($atividade['titulo']); ?></h3>
        <p><small>Atenção: Todas as notas associadas a esta atividade também serão apagadas.</small></p>
        <form method="post">
            <button type="submit" class="btn btn-delete">Sim, Excluir</button>
            <a href="atividades.php" class="btn" style="background-color: #ccc; display: block; margin-top: 10px; text-decoration: none;">Cancelar</a>
        </form>
    </div>
</body>
</html>