<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['matricula_validada'])) {
    die("Acesso negado. Por favor, valide sua matrícula e código primeiro.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_aluno_pendente = $_POST['id_aluno_pendente'];
    $matricula = $_POST['matricula'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $data_nascimento = $_POST['data_nascimento'];
    $id_perfil_aluno = 1;

    if ($senha !== $confirmar_senha) {
        die("Erro: As senhas não coincidem. Por favor, <a href='javascript:history.back()'>volte</a> e tente novamente.");
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $conexao->begin_transaction();

    try {
        $sql_usuarios = "INSERT INTO usuarios (id_perfil, nome, email, senha) VALUES (?, ?, ?, ?)";
        $stmt_usuarios = $conexao->prepare($sql_usuarios);
        $stmt_usuarios->bind_param("isss", $id_perfil_aluno, $nome, $email, $senha_hash);
        $stmt_usuarios->execute();
        $id_novo_usuario = $conexao->insert_id;

        $sql_alunos = "INSERT INTO dados_alunos (id_usuario, matricula, data_nascimento) VALUES (?, ?, ?)";
        $stmt_alunos = $conexao->prepare($sql_alunos);
        $stmt_alunos->bind_param("iss", $id_novo_usuario, $matricula, $data_nascimento);
        $stmt_alunos->execute();

        $sql_pendentes = "UPDATE alunos_pendentes SET status = 'concluido' WHERE id = ?";
        $stmt_pendentes = $conexao->prepare($sql_pendentes);
        $stmt_pendentes->bind_param("i", $id_aluno_pendente);
        $stmt_pendentes->execute();

        $conexao->commit();
        session_destroy();
        
        echo "<h1>Cadastro finalizado com sucesso!</h1>";
        echo "<p>Sua conta foi criada. Agora você já pode fazer o login no sistema.</p>";
        echo '<a href="login.php">Fazer Login</a>';

    } catch (mysqli_sql_exception $exception) {
        $conexao->rollback();
        die("Erro ao finalizar o cadastro: " . $exception->getMessage());
    }

    $stmt_usuarios->close();
    $stmt_alunos->close();
    $stmt_pendentes->close();
    $conexao->close();

} else {
    echo "Acesso inválido.";
}
?>