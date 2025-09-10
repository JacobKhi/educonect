<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

// Segurança: só pode aceder a este script se tiver validado o código
if (!isset($_SESSION['id_resp_pendente'])) {
    die("Acesso negado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pendente = $_SESSION['id_resp_pendente'];
    $nome = $_SESSION['nome_completo_validado'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $id_perfil_responsavel = 3;

    // Validação de senha
    if ($senha !== $confirmar_senha) {
        $_SESSION['error_message'] = "As senhas não coincidem.";
        $_SESSION['form_data'] = $_POST;
        header("Location: completar_cadastro_responsavel.php");
        exit();
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    
    // Transação para garantir a integridade dos dados
    $conexao->begin_transaction();
    try {
        // 1. Inserir na tabela de utilizadores
        $sql_usuarios = "INSERT INTO usuarios (id_perfil, nome, email, senha) VALUES (?, ?, ?, ?)";
        $stmt_usuarios = $conexao->prepare($sql_usuarios);
        $stmt_usuarios->bind_param("isss", $id_perfil_responsavel, $nome, $email, $senha_hash);
        $stmt_usuarios->execute();

        // 2. Atualizar o status do responsável pendente para 'concluido'
        $sql_pendentes = "UPDATE responsaveis_pendentes SET status = 'concluido' WHERE id = ?";
        $stmt_pendentes = $conexao->prepare($sql_pendentes);
        $stmt_pendentes->bind_param("i", $id_pendente);
        $stmt_pendentes->execute();

        // Se tudo correu bem, confirma as alterações
        $conexao->commit();
        session_destroy();

        // Mensagem de sucesso
        echo "<!DOCTYPE html><html><head><title>Sucesso</title><link rel='stylesheet' href='" . BASE_URL . "/css/global.css'><link rel='stylesheet' href='" . BASE_URL . "/css/forms.css'><style>.msg-sucesso{max-width:500px; margin:50px auto; padding:20px; text-align:center; background-color: #d4edda; color: #155724; border:1px solid #c3e6cb; border-radius:5px;}</style></head><body>";
        echo "<div class='msg-sucesso'><h1>Cadastro de responsável finalizado com sucesso!</h1><p>A sua conta foi criada.</p><br><a href='" . BASE_URL . "/auth/login/login.php'>Fazer Login</a></div>";
        echo "</body></html>";
        exit();

    } catch (mysqli_sql_exception $exception) {
        $conexao->rollback(); // Desfaz as alterações em caso de erro
        
        // Verifica se o erro é de email duplicado
        if ($conexao->errno == 1062) {
            $_SESSION['error_message'] = "Este email já está em uso. Por favor, escolha outro.";
        } else {
            $_SESSION['error_message'] = "Erro ao finalizar o cadastro: " . $exception->getMessage();
        }
        
        $_SESSION['form_data'] = $_POST;
        header("Location: completar_cadastro_responsavel.php");
        exit();
    }
}
?>