<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

require_once BASE_PATH . '/includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultado do Cadastro</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/forms.css">
</head>
<body>
<div class="form-container">
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_aluno = $_POST['nome_completo_aluno'];
    $nome_responsavel = $_POST['nome_completo_responsavel'];
    $conexao->begin_transaction();
    try {
        $codigo_aluno = uniqid('ALUNO_');
        $sql_aluno_insert = "INSERT INTO alunos_pendentes (nome_completo, codigo_cadastro) VALUES (?, ?)";
        $stmt_aluno = $conexao->prepare($sql_aluno_insert);
        $stmt_aluno->bind_param("ss", $nome_aluno, $codigo_aluno);
        $stmt_aluno->execute();
        $id_aluno_pendente = $conexao->insert_id;
        $matricula_aluno = 'ALUN-' . str_pad($id_aluno_pendente, 4, '0', STR_PAD_LEFT);
        $sql_aluno_update = "UPDATE alunos_pendentes SET matricula = ? WHERE id = ?";
        $stmt_aluno_update = $conexao->prepare($sql_aluno_update);
        $stmt_aluno_update->bind_param("si", $matricula_aluno, $id_aluno_pendente);
        $stmt_aluno_update->execute();
        
        echo "<h2>Aluno pré-cadastrado com sucesso!</h2>";
        echo "<strong>Matrícula do Aluno:</strong> " . htmlspecialchars($matricula_aluno) . "<br>";
        echo "<strong>Código de Cadastro do Aluno:</strong> " . htmlspecialchars($codigo_aluno) . "<br>";

        if (!empty($nome_responsavel)) {
            $codigo_responsavel = uniqid('RESP_');
            $sql_resp = "INSERT INTO responsaveis_pendentes (nome_completo, matricula_aluno_associado, codigo_cadastro) VALUES (?, ?, ?)";
            $stmt_resp = $conexao->prepare($sql_resp);
            $stmt_resp->bind_param("sss", $nome_responsavel, $matricula_aluno, $codigo_responsavel);
            $stmt_resp->execute();
            
            echo "<hr style='margin: 20px 0;'>";
            echo "<h2>Responsável pré-cadastrado com sucesso!</h2>";
            echo "<strong>Vinculado ao aluno:</strong> " . htmlspecialchars($nome_aluno) . "<br>";
            echo "<strong>Código de Cadastro do Responsável:</strong> " . htmlspecialchars($codigo_responsavel) . "<br>";
        }
        
        $conexao->commit();
        echo '<br><a href="../cadastrar/aluno.php">Cadastrar outro aluno</a>';
        echo '<br><a href="../painel.php">Voltar ao Painel</a>';
    } catch (mysqli_sql_exception $exception) {
        $conexao->rollback();
        echo "<h2>Erro ao cadastrar.</h2><p>Ocorreu um erro: " . $exception->getMessage() . "</p>";
        echo '<br><a href="../cadastrar/aluno.php">Tentar Novamente</a>';
    }
}
?>
</div>
</body>
</html>