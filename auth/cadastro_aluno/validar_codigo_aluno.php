<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matricula = $_POST['matricula'];
    $codigo_cadastro = $_POST['codigo_cadastro'];

    $sql = "SELECT id, nome_completo, matricula FROM alunos_pendentes WHERE matricula = ? AND codigo_cadastro = ? AND status = 'pendente'";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ss", $matricula, $codigo_cadastro);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $aluno_pendente = $resultado->fetch_assoc();
        $_SESSION['id_aluno_pendente'] = $aluno_pendente['id'];
        $_SESSION['nome_completo_validado'] = $aluno_pendente['nome_completo'];
        $_SESSION['matricula_validada'] = $aluno_pendente['matricula'];

        header("Location: completar_cadastro_aluno.php");
        exit();
    } else {
        $_SESSION['login_error'] = "Matrícula ou Código de Cadastro inválido.";
        header("Location: finalizar_cadastro_aluno.php");
        exit();
    }
} else {
    header("Location: finalizar_cadastro_aluno.php");
    exit();
}
?>