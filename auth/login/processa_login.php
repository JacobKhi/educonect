<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha_digitada = $_POST['senha'];

    $sql = "SELECT id, nome, senha, id_perfil FROM usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        if (password_verify($senha_digitada, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_perfil_id'] = $usuario['id_perfil'];
            
            // Redirecionamento correto para o dashboard na raiz do projeto
            header("Location: " . BASE_URL . "/dashboard.php");
            exit();
        }
    }
    
    $_SESSION['login_error'] = "Email ou senha inválidos.";
    // Redireciona de volta para o formulário de login na mesma pasta
    header("Location: login.php");
    exit();

} else {
    header("Location: login.php");
    exit();
}
?>