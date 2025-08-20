<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

// Bloco de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_aluno'])) {
    $id_aluno = $_POST['id_aluno'];
    $id_turma = $_POST['id_turma'];

    $sql = "INSERT INTO alunos_turmas (id_aluno_usuario, id_turma) VALUES (?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $id_aluno, $id_turma);
    $stmt->execute();
    $stmt->close();
    
    header("Location: matricular_alunos.php");
    exit();
}

$sql_alunos = "SELECT id, nome FROM usuarios WHERE id_perfil = 1 ORDER BY nome ASC";
$result_alunos = $conexao->query($sql_alunos);

$sql_turmas = "SELECT id, nome FROM turmas ORDER BY nome ASC";
$result_turmas = $conexao->query($sql_turmas);

$sql_matriculas = "
    SELECT 
        u.nome AS nome_aluno,
        t.nome AS nome_turma
    FROM 
        alunos_turmas at
    -- CORREÇÃO AQUI: trocado aluno_usuario_id por id_aluno_usuario
    JOIN usuarios u ON at.id_aluno_usuario = u.id
    JOIN turmas t ON at.id_turma = t.id
    ORDER BY nome_turma, nome_aluno;
";
$result_matriculas = $conexao->query($sql_matriculas);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Matricular Alunos</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>

    <div class="container">
        <div class="content-box">
            <h1>Matricular Alunos em Turmas</h1>
            <div class="form-and-list-layout">
                <div class="form-section">
                    <h3>Nova Matrícula</h3>
                    <form action="matricular_alunos.php" method="post">
                        <div class="form-group">
                            <label for="id_aluno">Aluno:</label>
                            <select id="id_aluno" name="id_aluno" required style="width: 100%; padding: 8px; margin-bottom: 15px;">
                                <option value="">-- Selecione o aluno --</option>
                                <?php if ($result_alunos) { while($aluno = $result_alunos->fetch_assoc()): ?>
                                    <option value="<?php echo $aluno['id']; ?>"><?php echo htmlspecialchars($aluno['nome']); ?></option>
                                <?php endwhile; } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_turma">Turma:</label>
                            <select id="id_turma" name="id_turma" required style="width: 100%; padding: 8px; margin-bottom: 15px;">
                                <option value="">-- Selecione a turma --</option>
                                <?php if ($result_turmas) { while($turma = $result_turmas->fetch_assoc()): ?>
                                    <option value="<?php echo $turma['id']; ?>"><?php echo htmlspecialchars($turma['nome']); ?></option>
                                <?php endwhile; } ?>
                            </select>
                        </div>
                        <button type="submit" class="btn">Matricular Aluno</button>
                    </form>
                </div>

                <div class="list-section">
                    <h3>Alunos Matriculados</h3>
                    <?php if ($result_matriculas && $result_matriculas->num_rows > 0): ?>
                        <table>
                            <thead><tr><th>Turma</th><th>Aluno</th></tr></thead>
                            <tbody>
                                <?php while($mat = $result_matriculas->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($mat['nome_turma']); ?></td>
                                        <td><?php echo htmlspecialchars($mat['nome_aluno']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Nenhum aluno matriculado em turmas ainda.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>
<?php if ($conexao) { $conexao->close(); } ?>