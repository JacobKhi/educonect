<?php
session_start();
require_once __DIR__ . '/../../config.php';

if (!isset($_SESSION['matricula_prof_validada'])) {
    header("Location: finalizar_cadastro_professor.php");
    exit();
}

$nome = htmlspecialchars($_SESSION['nome_completo_validado']);
$matricula = htmlspecialchars($_SESSION['matricula_prof_validada']);

$mensagem_erro = $_SESSION['error_message'] ?? '';
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['error_message'], $_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Completar Cadastro de Professor</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/forms.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="form-container">
        <div class="info">
            <h2>Olá, Prof. <?php echo $nome; ?>!</h2>
            <p>Sua matrícula é: <strong><?php echo $matricula; ?></strong></p>
        </div>
        <form action="processa_cadastro_final_professor.php" method="post">
            <h3>Preencha os dados para criar sua conta.</h3>
            <?php if (!empty($mensagem_erro)) { echo "<div class='erro'>$mensagem_erro</div>"; } ?>
            
            <label for="email">Seu Email de Contato:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>

            <label for="senha">Crie uma Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <label for="confirmar_senha">Confirme sua Senha:</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" required>

            <label for="especializacao">Sua Principal Área de Especialização:</label>
            <input type="text" id="especializacao" name="especializacao" value="<?php echo htmlspecialchars($form_data['especializacao'] ?? ''); ?>" placeholder="Ex: Matemática" required>

            <button type="submit">Finalizar Cadastro</button>
        </form>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>