<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

// Bloco de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

require_once BASE_PATH . '/includes/navbar.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['nome_completo_responsavel'])) {
    $nome_responsavel = $_POST['nome_completo_responsavel'];
    $codigo_cadastro = 'RESP_' . uniqid(); // Gera um código único

    $sql = "INSERT INTO responsaveis_pendentes (nome_completo, codigo_cadastro) VALUES (?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ss", $nome_responsavel, $codigo_cadastro);
    
    $mensagem = "";
    if ($stmt->execute()) {
        $mensagem = "<h2>Responsável pré-cadastrado com sucesso!</h2>";
        $mensagem .= "<p>Por favor, entregue o seguinte código ao responsável para que ele possa finalizar o cadastro:</p>";
        $mensagem .= "<strong>Código de Cadastro:</strong> " . htmlspecialchars($codigo_cadastro);
        $mensagem .= '<br><br><a href="../cadastrar/responsavel.php">Cadastrar outro responsável</a>';
        $mensagem .= '<br><a href="../painel.php">Voltar ao Painel</a>';
    } else {
        $mensagem = "<h2>Erro ao cadastrar.</h2><p>Ocorreu um erro inesperado. Tente novamente.</p>";
        $mensagem .= '<br><a href="../cadastrar/responsavel.php">Tentar Novamente</a>';
    }
    $stmt->close();
    $conexao->close();
} else {
    header("Location: ../cadastrar/responsavel.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultado do Cadastro</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/forms.css">
</head>
<body class="center-layout">
    <div class="form-container" style="text-align: center;">
        <?php echo $mensagem; ?>
    </div>
</body>
</html>