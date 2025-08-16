<?php
session_start();
require_once '../conexao.php';

// Garantir que apenas gestores acessem
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultado do Cadastro</title>
    <link rel="stylesheet" href="/educonect/css/global.css">
    <link rel="stylesheet" href="/educonect/css/navbar.css">
    <link rel="stylesheet" href="/educonect/css/forms.css">
</head>
<body>
<div class="form-container">
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_completo = $_POST['nome_completo'];
    
    $codigo_cadastro = uniqid('PROF_');
    $conexao->begin_transaction();

    try {
        $sql_insert = "INSERT INTO professores_pendentes (nome_completo, codigo_cadastro) VALUES (?, ?)";
        $stmt_insert = $conexao->prepare($sql_insert);
        $stmt_insert->bind_param("ss", $nome_completo, $codigo_cadastro);
        $stmt_insert->execute();
        
        $id_prof_pendente = $conexao->insert_id;

        $matricula_formatada = 'PROF-' . str_pad($id_prof_pendente, 4, '0', STR_PAD_LEFT);

        $sql_update = "UPDATE professores_pendentes SET matricula_professor = ? WHERE id = ?";
        $stmt_update = $conexao->prepare($sql_update);
        $stmt_update->bind_param("si", $matricula_formatada, $id_prof_pendente);
        $stmt_update->execute();
        
        $conexao->commit();

        echo "<h2>Professor pré-cadastrado com sucesso!</h2>";
        echo "<p>Por favor, entregue os seguintes dados ao professor:</p>";
        echo "<strong>Matrícula:</strong> " . htmlspecialchars($matricula_formatada) . "<br>";
        echo "<strong>Código de Cadastro:</strong> " . htmlspecialchars($codigo_cadastro) . "<br>";
        echo '<br><a href="../cadastrar/professor.php">Cadastrar outro professor</a>';
        echo '<br><a href="../painel.php">Voltar ao Painel</a>';

    } catch (mysqli_sql_exception $exception) {
        $conexao->rollback();
        echo "<h2>Erro ao cadastrar.</h2>";
        echo "<p>Ocorreu um erro inesperado: " . $exception->getMessage() . "</p>";
        echo '<br><a href="admin_cadastrar_professor.php">Tentar Novamente</a>';
    }

    $stmt_insert->close();
    $stmt_update->close();
    $conexao->close();
}
?>
</div>
</body>
</html>