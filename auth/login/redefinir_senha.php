<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';
require_once BASE_PATH . '/includes/navbar.php';

$token = $_GET['token'] ?? null;
$mensagem_erro = '';
$mensagem_sucesso = '';
$token_valido = false;
$id_usuario = null;

if ($token) {
    // Verifica se o token é válido e não expirou
    $stmt = $conexao->prepare("SELECT id_usuario, data_expiracao FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $reset = $resultado->fetch_assoc();
        $agora = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
        $data_expiracao = new DateTime($reset['data_expiracao']);

        if ($agora < $data_expiracao) {
            $token_valido = true;
            $id_usuario = $reset['id_usuario'];
        } else {
            $mensagem_erro = "Este link de recuperação expirou. Por favor, solicite um novo.";
        }
    } else {
        $mensagem_erro = "Link de recuperação inválido.";
    }
} else {
    $mensagem_erro = "Nenhum token de recuperação fornecido.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $token_valido) {
    $nova_senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if ($nova_senha === $confirmar_senha) {
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        // Atualiza a senha do usuário
        $stmt_update = $conexao->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        $stmt_update->bind_param("si", $senha_hash, $id_usuario);
        $stmt_update->execute();

        // Invalida o token
        $stmt_delete = $conexao->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt_delete->bind_param("s", $token);
        $stmt_delete->execute();

        $mensagem_sucesso = "Sua senha foi redefinida com sucesso! Você já pode fazer o login.";
        $token_valido = false; // Impede que o formulário seja mostrado novamente

    } else {
        $mensagem_erro = "As senhas não coincidem.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/forms.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
</head>
<body class="center-layout">
    <div class="form-container">
        <h2>Redefinir Senha</h2>
        
        <?php if (!empty($mensagem_erro)): ?>
            <div class='erro'><?php echo $mensagem_erro; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($mensagem_sucesso)): ?>
            <div class='sucesso'><?php echo $mensagem_sucesso; ?></div>
            <a href="login.php" class="btn" style="display: block; text-align: center; background-color: #1E3A8A; text-decoration:none;">Ir para Login</a>
        <?php endif; ?>

        <?php if ($token_valido): ?>
        <form action="redefinir_senha.php?token=<?php echo htmlspecialchars($token); ?>" method="post">
            <label for="senha">Nova Senha:</label>
            <input type="password" id="senha" name="senha" required>
            <label for="confirmar_senha">Confirme a Nova Senha:</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" required>
            <button type="submit">Redefinir Senha</button>
        </form>
        <?php endif; ?>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>