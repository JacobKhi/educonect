<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';
require_once BASE_PATH . '/includes/navbar.php';

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Verifica se o email existe na base de dados
    $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        $id_usuario = $usuario['id'];

        // Gera um token seguro
        $token = bin2hex(random_bytes(50));
        // Define a expiração para 1 hora a partir de agora
        $expira = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
        $expira->add(new DateInterval('PT1H'));
        $data_expiracao = $expira->format('Y-m-d H:i:s');

        // Insere o token na base de dados
        $stmt_insert = $conexao->prepare("INSERT INTO password_resets (id_usuario, token, data_expiracao) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("iss", $id_usuario, $token, $data_expiracao);
        $stmt_insert->execute();

        // --- SIMULAÇÃO DE ENVIO DE E-MAIL ---
        $link_recuperacao = BASE_URL . "/auth/login/redefinir_senha.php?token=" . $token;
        $mensagem = "<strong>Sucesso!</strong> Um link de recuperação foi gerado. Em um sistema real, este link seria enviado para o seu e-mail.<br><br><strong>Link para teste:</strong> <a href='{$link_recuperacao}'>{$link_recuperacao}</a>";
        $tipo_mensagem = 'sucesso';

    } else {
        $mensagem = "Se o e-mail informado existir em nossa base de dados, um link de recuperação será enviado.";
        $tipo_mensagem = 'sucesso'; // Mensagem genérica por segurança
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/forms.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
</head>
<body class="center-layout">
    <div class="form-container">
        <form action="solicitar_recuperacao.php" method="post">
            <h2>Recuperar Senha</h2>
            <p style="text-align: center; margin-bottom: 20px;">Insira o seu e-mail de cadastro. Se ele estiver em nossa base de dados, enviaremos um link para você redefinir sua senha.</p>
            
            <?php if (!empty($mensagem)): ?>
                <div class='<?php echo $tipo_mensagem; ?>' style="text-align: left; word-wrap: break-word;"><?php echo $mensagem; ?></div>
            <?php endif; ?>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <button type="submit">Enviar Link de Recuperação</button>
        </form>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>