<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

$mensagem_erro = '';
$nome = '';
$matricula = '';
$email = '';
$data_nascimento = '';

if (!isset($_SESSION['matricula_validada'])) {
    header("Location: finalizar_cadastro_aluno.php");
    exit();
} else {
    $nome = htmlspecialchars($_SESSION['nome_completo_validado']);
    $matricula = htmlspecialchars($_SESSION['matricula_validada']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_aluno_pendente = $_SESSION['id_aluno_pendente'];
    $nome_v = $_SESSION['nome_completo_validado'];
    $matricula_v = $_SESSION['matricula_validada'];
    $id_perfil_aluno = 1; 
    $email = $_POST['email'];
    $data_nascimento = $_POST['data_nascimento'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if ($senha !== $confirmar_senha) {
        $mensagem_erro = "As senhas não coincidem. Por favor, tente novamente.";
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $conexao->begin_transaction();
        try {
            $sql_usuarios = "INSERT INTO usuarios (id_perfil, nome, email, senha) VALUES (?, ?, ?, ?)";
            $stmt_usuarios = $conexao->prepare($sql_usuarios);
            $stmt_usuarios->bind_param("isss", $id_perfil_aluno, $nome_v, $email, $senha_hash);
            $stmt_usuarios->execute();
            $id_novo_usuario = $conexao->insert_id;

            $sql_alunos = "INSERT INTO dados_alunos (id_usuario, matricula, data_nascimento) VALUES (?, ?, ?)";
            $stmt_alunos = $conexao->prepare($sql_alunos);
            $stmt_alunos->bind_param("iss", $id_novo_usuario, $matricula_v, $data_nascimento);
            $stmt_alunos->execute();

            $sql_pendentes = "UPDATE alunos_pendentes SET status = 'concluido' WHERE id = ?";
            $stmt_pendentes = $conexao->prepare($sql_pendentes);
            $stmt_pendentes->bind_param("i", $id_aluno_pendente);
            $stmt_pendentes->execute();

            $conexao->commit();
            session_destroy();

            echo "<!DOCTYPE html><html><head><title>Sucesso</title><link rel='stylesheet' href='" . BASE_URL . "/css/global.css'><link rel='stylesheet' href='" . BASE_URL . "/css/forms.css'><style>.msg-sucesso{max-width:500px; margin:50px auto; padding:20px; text-align:center; background-color: #d4edda; color: #155724; border:1px solid #c3e6cb; border-radius:5px;}</style></head><body>";
            echo "<div class='msg-sucesso'><h1>Cadastro finalizado com sucesso!</h1><p>Sua conta foi criada.</p><br><a href='" . BASE_URL . "/auth/login/login.php'>Fazer Login</a></div>";
            echo "</body></html>";
            exit();

        } catch (mysqli_sql_exception $exception) {
            $conexao->rollback();
            if ($conexao->errno == 1062) {
                $mensagem_erro = "Este email já está em uso. Por favor, escolha outro.";
            } else {
                $mensagem_erro = "Erro ao finalizar o cadastro: " . $exception->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Completar Cadastro de Aluno</title>
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
            <p>Sua matrícula é: <strong><?php echo $matricula; ?></strong></p>
        </div>
        <form action="completar_cadastro_aluno.php" method="post">
            <h3>Falta pouco! Preencha os dados abaixo.</h3>
            <?php
            if (!empty($mensagem_erro)) {
                echo "<div class='erro'>$mensagem_erro</div>";
            }
            ?>
            <label for="email">Seu melhor Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            <label for="senha">Crie uma Senha:</label>
            <input type="password" id="senha" name="senha" required>
            <label for="confirmar_senha">Confirme sua Senha:</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" required>
            <label for="data_nascimento">Data de Nascimento:</label>
            <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($data_nascimento); ?>" required>
            <button type="submit">Finalizar Cadastro</button>
        </form>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>