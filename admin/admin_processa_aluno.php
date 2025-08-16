<?php
require_once 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_completo = $_POST['nome_completo'];

    $codigo_cadastro = uniqid('ALUNO_');

    $conexao->begin_transaction();

    try {
        $sql_insert = "INSERT INTO alunos_pendentes (nome_completo, codigo_cadastro) VALUES (?, ?)";
        $stmt_insert = $conexao->prepare($sql_insert);
        $stmt_insert->bind_param("ss", $nome_completo, $codigo_cadastro);
        $stmt_insert->execute();
        
        $id_aluno_pendente = $conexao->insert_id;

        $matricula_formatada = 'ALUN-' . str_pad($id_aluno_pendente, 4, '0', STR_PAD_LEFT);

        $sql_update = "UPDATE alunos_pendentes SET matricula = ? WHERE id = ?";
        $stmt_update = $conexao->prepare($sql_update);
        $stmt_update->bind_param("si", $matricula_formatada, $id_aluno_pendente);
        $stmt_update->execute();
        
        $conexao->commit();

        echo "<h2>Aluno pré-cadastrado com sucesso!</h2>";
        echo "<p>Por favor, entregue os seguintes dados ao aluno:</p>";
        echo "<strong>Matrícula:</strong> " . htmlspecialchars($matricula_formatada) . "<br>";
        echo "<strong>Código de Cadastro:</strong> " . htmlspecialchars($codigo_cadastro) . "<br>";
        echo '<br><a href="admin_cadastrar_aluno.php">Cadastrar outro aluno</a>';

    } catch (mysqli_sql_exception $exception) {
        $conexao->rollback();
        echo "Erro ao pré-cadastrar aluno: " . $exception->getMessage();
    }

    $stmt_insert->close();
    $stmt_update->close();
    $conexao->close();

} else {
    echo "Acesso inválido.";
}
?>