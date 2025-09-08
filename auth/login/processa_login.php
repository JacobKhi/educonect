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
            
            $url_destino = BASE_URL . '/dashboard.php';

            switch ($usuario['id_perfil']) {
                case 2: // Professor
                    $url_destino = BASE_URL . '/professor/painel.php';
                    break;
                case 4: // Admin/Gestor
                    $url_destino = BASE_URL . '/admin/painel.php';
                    break;
            }
            
            header("Location: " . $url_destino);
            exit();
        }
    }
    
    $_SESSION['login_error'] = "Email ou senha inválidos.";
    header("Location: login.php");
    exit();

} else {
    header("Location: login.php");
    exit();
}
?>