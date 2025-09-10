<?php
session_start();
require_once __DIR__ . '/../../config.php';

// Se o responsável não validou o código, ele não pode estar aqui.
if (!isset($_SESSION['id_resp_pendente'])) {
    header("Location: finalizar_cadastro_responsavel.php");
    exit();
}

$nome = htmlspecialchars($_SESSION['nome_completo_validado']);

// Para repopular o formulário em caso de erro
$mensagem_erro = $_SESSION['error_message'] ?? '';
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['error_message'], $_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Completar Cadastro de Responsável</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/forms.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="form-container">
        <div class="info">
            <h2>Olá, <?php echo $nome; ?>!</h2>
            <p>Falta pouco! Preencha os dados abaixo para criar a sua conta.</p>
        </div>
        <form action="processa_cadastro_final_responsavel.php" method="post">
            <?php if (!empty($mensagem_erro)) { echo "<div class='erro'>$mensagem_erro</div>"; } ?>
            
            <label for="email">O seu melhor Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>

            <label for="senha">Crie uma Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <label for="confirmar_senha">Confirme a sua Senha:</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" required>

            <button type="submit">Finalizar Cadastro</button>
        </form>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>