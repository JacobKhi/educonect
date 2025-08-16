<?php
session_start();
require_once '../conexao.php';
require_once '../includes/navbar.php';

// Garantir que apenas gestores acessem
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: ../auth/login.php");
    exit();
}

$sql_alunos = "SELECT matricula, nome_completo, codigo_cadastro FROM alunos_pendentes WHERE status = 'pendente' ORDER BY nome_completo ASC";
$resultado_alunos = $conexao->query($sql_alunos);

$sql_professores = "SELECT matricula_professor, nome_completo, codigo_cadastro FROM professores_pendentes WHERE status = 'pendente' ORDER BY nome_completo ASC";
$resultado_professores = $conexao->query($sql_professores);

$sql_responsaveis = "SELECT nome_completo, matricula_aluno_associado, codigo_cadastro FROM responsaveis_pendentes WHERE status = 'pendente' ORDER BY nome_completo ASC";
$resultado_responsaveis = $conexao->query($sql_responsaveis);

$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Cadastros Pendentes</title>
    <link rel="stylesheet" href="/educonect/css/global.css">
    <link rel="stylesheet" href="/educonect/css/navbar.css">
    <link rel="stylesheet" href="/educonect/css/footer.css">
    <style>
        .container { max-width: 1000px; margin: 20px auto; padding: 20px; background-color: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1, h2 { border-bottom: 2px solid #f2f2f2; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Cadastros Pendentes</h1>
        <p>Abaixo estão todos os usuários que foram pré-cadastrados mas ainda não finalizaram seu primeiro acesso.</p>

        <h2>Alunos Pendentes</h2>
        <?php if ($resultado_alunos->num_rows > 0): ?>
            <table>
                <thead>
                    <tr><th>Nome Completo</th><th>Matrícula</th><th>Código de Cadastro</th></tr>
                </thead>
                <tbody>
                    <?php while($aluno = $resultado_alunos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($aluno['nome_completo']); ?></td>
                            <td><?php echo htmlspecialchars($aluno['matricula']); ?></td>
                            <td><?php echo htmlspecialchars($aluno['codigo_cadastro']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum aluno pendente no momento.</p>
        <?php endif; ?>

        <h2 style="margin-top: 40px;">Professores Pendentes</h2>
        <?php if ($resultado_professores->num_rows > 0): ?>
            <table>
                 <thead>
                    <tr><th>Nome Completo</th><th>Matrícula</th><th>Código de Cadastro</th></tr>
                </thead>
                <tbody>
                    <?php while($prof = $resultado_professores->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($prof['nome_completo']); ?></td>
                            <td><?php echo htmlspecialchars($prof['matricula_professor']); ?></td>
                            <td><?php echo htmlspecialchars($prof['codigo_cadastro']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum professor pendente no momento.</p>
        <?php endif; ?>

        <h2 style="margin-top: 40px;">Responsáveis Pendentes</h2>
        <?php if ($resultado_responsaveis->num_rows > 0): ?>
            <table>
                <thead>
                    <tr><th>Nome do Responsável</th><th>Aluno Associado (Matrícula)</th><th>Código de Cadastro</th></tr>
                </thead>
                <tbody>
                    <?php while($resp = $resultado_responsaveis->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($resp['nome_completo']); ?></td>
                            <td><?php echo htmlspecialchars($resp['matricula_aluno_associado']); ?></td>
                            <td><?php echo htmlspecialchars($resp['codigo_cadastro']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum responsável pendente no momento.</p>
        <?php endif; ?>
    </div>
    <?php require_once '../includes/footer.php'; ?>
</body>
</html>