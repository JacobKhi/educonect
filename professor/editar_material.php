<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/conexao.php';

// Bloco de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 2 || !isset($_GET['id'])) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

$id_material = $_GET['id'];

// Lógica para ATUALIZAR as informações do material
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];

    $sql_update = "UPDATE materiais_estudo SET titulo = ?, descricao = ? WHERE id = ?";
    $stmt_update = $conexao->prepare($sql_update);
    $stmt_update->bind_param("ssi", $titulo, $descricao, $id_material);
    $stmt_update->execute();
    $stmt_update->close();

    // Adicionaremos uma mensagem de sucesso aqui no futuro
    header("Location: materiais.php");
    exit();
}

// Busca os dados atuais do material para preencher o formulário
$sql_select = "SELECT titulo, descricao FROM materiais_estudo WHERE id = ?";
$stmt_select = $conexao->prepare($sql_select);
$stmt_select->bind_param("i", $id_material);
$stmt_select->execute();
$material = $stmt_select->get_result()->fetch_assoc();
$stmt_select->close();

if (!$material) {
    header("Location: materiais.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Material de Estudo</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/forms.css">
</head>
<body class="center-layout">
    <div class="form-container">
        <h2>Editar Material de Estudo</h2>
        <p style="text-align: center; margin-top: -15px; margin-bottom: 20px;"><small>(Apenas o título e a descrição podem ser alterados. Para mudar o ficheiro, exclua este e envie um novo.)</small></p>
        <form method="post">
            <div class="form-group">
                <label for="titulo">Título do Material:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($material['titulo']); ?>" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" rows="4"><?php echo htmlspecialchars($material['descricao']); ?></textarea>
            </div>
            <button type="submit" class="btn">Salvar Alterações</button>
        </form>
    </div>
</body>
</html>