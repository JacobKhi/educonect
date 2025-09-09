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

    // Evita duplicatas
    $check_sql = "SELECT * FROM alunos_turmas WHERE id_aluno_usuario = ? AND id_turma = ?";
    $check_stmt = $conexao->prepare($check_sql);
    $check_stmt->bind_param("ii", $id_aluno, $id_turma);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    if ($result->num_rows == 0) {
        $sql = "INSERT INTO alunos_turmas (id_aluno_usuario, id_turma) VALUES (?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ii", $id_aluno, $id_turma);
        $stmt->execute();
        $stmt->close();
    }
    $check_stmt->close();
    
    header("Location: matricular_alunos.php");
    exit();
}

$sql_alunos = "SELECT id, nome FROM usuarios WHERE id_perfil = 1 ORDER BY nome ASC";
$result_alunos = $conexao->query($sql_alunos);

$sql_turmas = "SELECT id, nome FROM turmas ORDER BY nome ASC";
$result_turmas = $conexao->query($sql_turmas);

// Busca as matrículas e já agrupa por turma no PHP
$sql_matriculas = "
    SELECT 
        u.id AS id_aluno,
        t.id AS id_turma,
        u.nome AS nome_aluno,
        t.nome AS nome_turma
    FROM 
        alunos_turmas at
    JOIN usuarios u ON at.id_aluno_usuario = u.id
    JOIN turmas t ON at.id_turma = t.id
    ORDER BY nome_turma, nome_aluno;
";
$result_matriculas = $conexao->query($sql_matriculas);

// Agrupando os resultados por turma
$matriculas_por_turma = [];
if ($result_matriculas) {
    while ($mat = $result_matriculas->fetch_assoc()) {
        $matriculas_por_turma[$mat['nome_turma']][] = $mat;
    }
}

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
                            <select id="id_aluno" name="id_aluno" required>
                                <option value="">-- Selecione o aluno --</option>
                                <?php if ($result_alunos) { mysqli_data_seek($result_alunos, 0); while($aluno = $result_alunos->fetch_assoc()): ?>
                                    <option value="<?php echo $aluno['id']; ?>"><?php echo htmlspecialchars($aluno['nome']); ?></option>
                                <?php endwhile; } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_turma">Turma:</label>
                            <select id="id_turma" name="id_turma" required>
                                <option value="">-- Selecione a turma --</option>
                                <?php if ($result_turmas) { mysqli_data_seek($result_turmas, 0); while($turma = $result_turmas->fetch_assoc()): ?>
                                    <option value="<?php echo $turma['id']; ?>"><?php echo htmlspecialchars($turma['nome']); ?></option>
                                <?php endwhile; } ?>
                            </select>
                        </div>
                        <button type="submit" class="btn">Matricular Aluno</button>
                    </form>
                </div>

                <div class="list-section">
                    <h3>Alunos Matriculados</h3>
                    <?php if (!empty($matriculas_por_turma)): ?>
                        <?php foreach ($matriculas_por_turma as $nome_turma => $alunos_na_turma): ?>
                            <details class="accordion-item">
                                <summary class="accordion-header"><?php echo htmlspecialchars($nome_turma); ?> (<?php echo count($alunos_na_turma); ?> alunos)</summary>
                                <div class="accordion-content">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Aluno</th>
                                                <th style="width: 120px;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($alunos_na_turma as $mat): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($mat['nome_aluno']); ?></td>
                                                    <td class="actions-cell">
                                                        <a href="excluir_matricula.php?id_aluno=<?php echo $mat['id_aluno']; ?>&id_turma=<?php echo $mat['id_turma']; ?>" class="btn btn-small btn-delete">Excluir</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </details>
                        <?php endforeach; ?>
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