<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

if (!isset($_SESSION['matricula_prof_validada'])) {
    die("Acesso negado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pendente = $_SESSION['id_prof_pendente'];
    $nome = $_SESSION['nome_completo_validado'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $especializacao = $_POST['especializacao'];
    $id_perfil_prof = 2;

    if ($senha !== $confirmar_senha) {
        $_SESSION['error_message'] = "As senhas não coincidem.";
        $_SESSION['form_data'] = $_POST;
        header("Location: completar_cadastro_professor.php");
        exit();
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $conexao->begin_transaction();
    try {
        $sql_usuarios = "INSERT INTO usuarios (id_perfil, nome, email, senha) VALUES (?, ?, ?, ?)";
        $stmt_usuarios = $conexao->prepare($sql_usuarios);
        $stmt_usuarios->bind_param("isss", $id_perfil_prof, $nome, $email, $senha_hash);
        $stmt_usuarios->execute();
        $id_novo_usuario = $conexao->insert_id;

        $sql_prof = "INSERT INTO dados_professores (id_usuario, especializacao) VALUES (?, ?)";
        $stmt_prof = $conexao->prepare($sql_prof);
        $stmt_prof->bind_param("is", $id_novo_usuario, $especializacao);
        $stmt_prof->execute();

        $sql_pendentes = "UPDATE professores_pendentes SET status = 'concluido' WHERE id = ?";
        $stmt_pendentes = $conexao->prepare($sql_pendentes);
        $stmt_pendentes->bind_param("i", $id_pendente);
        $stmt_pendentes->execute();

        $conexao->commit();
        session_destroy();

        echo "<!DOCTYPE html><html><head><title>Sucesso</title><link rel='stylesheet' href='" . BASE_URL . "/css/global.css'><link rel='stylesheet' href='" . BASE_URL . "/css/forms.css'><style>.msg-sucesso{max-width:500px; margin:50px auto; padding:20px; text-align:center; background-color: #d4edda; color: #155724; border:1px solid #c3e6cb; border-radius:5px;}</style></head><body>";
        echo "<div class='msg-sucesso'><h1>Cadastro de professor finalizado com sucesso!</h1><p>Sua conta foi criada.</p><br><a href='" . BASE_URL . "/auth/login/login.php'>Fazer Login</a></div>";        echo "</body></html>";
        exit();
    } catch (mysqli_sql_exception $exception) {
        $conexao->rollback();
        $_SESSION['error_message'] = "Erro ao finalizar o cadastro: " . $exception->getMessage();
        $_SESSION['form_data'] = $_POST;
        header("Location: completar_cadastro_professor.php");
        exit();
    }
}
?>