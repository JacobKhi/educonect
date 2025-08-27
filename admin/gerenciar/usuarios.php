<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

// Bloco de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

$sql_alunos = "SELECT u.nome, da.matricula FROM usuarios u JOIN dados_alunos da ON u.id = da.id_usuario WHERE u.id_perfil = 1 ORDER BY u.nome ASC";
$result_alunos = $conexao->query($sql_alunos);

$sql_professores = "SELECT nome, email FROM usuarios WHERE id_perfil = 2 ORDER BY nome ASC";
$result_professores = $conexao->query($sql_professores);

$sql_responsaveis = "SELECT nome, email FROM usuarios WHERE id_perfil = 3 ORDER BY nome ASC";
$result_responsaveis = $conexao->query($sql_responsaveis);

$sql_gestores = "SELECT nome, email FROM usuarios WHERE id_perfil = 4 ORDER BY nome ASC";
$result_gestores = $conexao->query($sql_gestores);

$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Usuários Ativos</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>

    <div class="container">
        <div class="content-box">
            <h1>Usuários Ativos no Sistema</h1>

            <h2>Alunos</h2>
            <?php if ($result_alunos && $result_alunos->num_rows > 0): ?>
                <table>
                    <thead><tr><th>Nome Completo</th><th>Matrícula</th></tr></thead>
                    <tbody>
                        <?php while($aluno = $result_alunos->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                                <td><?php echo htmlspecialchars($aluno['matricula']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum aluno com conta ativa encontrado.</p>
            <?php endif; ?>

            <h2 style="margin-top: 40px;">Professores</h2>
            <?php if ($result_professores && $result_professores->num_rows > 0): ?>
                <table>
                    <thead><tr><th>Nome Completo</th><th>Email</th></tr></thead>
                    <tbody>
                        <?php while($prof = $result_professores->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($prof['nome']); ?></td>
                                <td><?php echo htmlspecialchars($prof['email']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum professor com conta ativa encontrado.</p>
            <?php endif; ?>

            <h2 style="margin-top: 40px;">Responsáveis</h2>
            <?php if ($result_responsaveis && $result_responsaveis->num_rows > 0): ?>
                <table>
                    <thead><tr><th>Nome Completo</th><th>Email</th></tr></thead>
                    <tbody>
                        <?php while($resp = $result_responsaveis->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($resp['nome']); ?></td>
                                <td><?php echo htmlspecialchars($resp['email']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum responsável com conta ativa encontrado.</p>
            <?php endif; ?>
            
            <h2 style="margin-top: 40px;">Equipe Pedagógica (Gestores)</h2>
            <?php if ($result_gestores && $result_gestores->num_rows > 0): ?>
                <table>
                    <thead><tr><th>Nome Completo</th><th>Email</th></tr></thead>
                    <tbody>
                        <?php while($gestor = $result_gestores->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($gestor['nome']); ?></td>
                                <td><?php echo htmlspecialchars($gestor['email']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum gestor com conta ativa encontrado.</p>
            <?php endif; ?>

        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>