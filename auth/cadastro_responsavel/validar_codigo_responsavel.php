<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_cadastro = $_POST['codigo_cadastro'];

    $sql = "SELECT id, nome_completo FROM responsaveis_pendentes WHERE codigo_cadastro = ? AND status = 'pendente'";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $codigo_cadastro);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $resp_pendente = $resultado->fetch_assoc();
        $_SESSION['id_resp_pendente'] = $resp_pendente['id'];
        $_SESSION['nome_completo_validado'] = $resp_pendente['nome_completo'];
        
        header("Location: completar_cadastro_responsavel.php");
        exit();
    } else {
        $_SESSION['cadastro_error'] = "Código de Cadastro de responsável inválido ou já utilizado.";
        header("Location: finalizar_cadastro_responsavel.php");
        exit();
    }
} else {
    header("Location: finalizar_cadastro_responsavel.php");
    exit();
}
?>