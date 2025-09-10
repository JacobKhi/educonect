<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/conexao.php';

// Garantir que apenas gestores acedam
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/login.php");
    exit();
}

$sql_alunos = "SELECT matricula, nome_completo, codigo_cadastro FROM alunos_pendentes WHERE status = 'pendente' ORDER BY nome_completo ASC";
$resultado_alunos = $conexao->query($sql_alunos);

$sql_professores = "SELECT matricula_professor, nome_completo, codigo_cadastro FROM professores_pendentes WHERE status = 'pendente' ORDER BY nome_completo ASC";
$resultado_professores = $conexao->query($sql_professores);

$sql_responsaveis = "SELECT nome_completo, codigo_cadastro FROM responsaveis_pendentes WHERE status = 'pendente' ORDER BY nome_completo ASC";
$resultado_responsaveis = $conexao->query($sql_responsaveis);

$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Cadastros Pendentes</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <div class="content-box">
            <h1>Cadastros Pendentes</h1>
            <p>Abaixo estão todos os utilizadores que foram pré-cadastrados mas ainda não finalizaram o seu primeiro acesso.</p>

            <details class="accordion-item" open>
                <summary class="accordion-header">Alunos Pendentes (<?php echo $resultado_alunos->num_rows; ?>)</summary>
                <div class="accordion-content">
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
                </div>
            </details>

            <details class="accordion-item">
                <summary class="accordion-header">Professores Pendentes (<?php echo $resultado_professores->num_rows; ?>)</summary>
                <div class="accordion-content">
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
                </div>
            </details>

            <details class="accordion-item">
                <summary class="accordion-header">Responsáveis Pendentes (<?php echo $resultado_responsaveis->num_rows; ?>)</summary>
                <div class="accordion-content">
                    <?php if ($resultado_responsaveis->num_rows > 0): ?>
                        <table>
                            <thead>
                                <tr><th>Nome do Responsável</th><th>Código de Cadastro</th></tr>
                            </thead>
                            <tbody>
                                <?php while($resp = $resultado_responsaveis->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($resp['nome_completo']); ?></td>
                                        <td><?php echo htmlspecialchars($resp['codigo_cadastro']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Nenhum responsável pendente no momento.</p>
                    <?php endif; ?>
                </div>
            </details>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>