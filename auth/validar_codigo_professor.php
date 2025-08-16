<?php
session_start();
require_once '../conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matricula_professor = $_POST['matricula_professor'];
    $codigo_cadastro = $_POST['codigo_cadastro'];

    $sql = "SELECT id, nome_completo, matricula_professor FROM professores_pendentes WHERE matricula_professor = ? AND codigo_cadastro = ? AND status = 'pendente'";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ss", $matricula_professor, $codigo_cadastro);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $prof_pendente = $resultado->fetch_assoc();

        $_SESSION['id_prof_pendente'] = $prof_pendente['id'];
        $_SESSION['nome_completo_validado'] = $prof_pendente['nome_completo'];
        $_SESSION['matricula_prof_validada'] = $prof_pendente['matricula_professor'];

        header("Location: completar_cadastro_professor.php");
        exit();

    } else {
        $_SESSION['login_error'] = "Matrícula ou Código de Cadastro de professor inválido.";
        header("Location: finalizar_cadastro_professor.php");
        exit();
    }

    $stmt->close();
    $conexao->close();

} else {
    header("Location: finalizar_cadastro_professor.php");
    exit();
}
?>