<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 2) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

$id_professor = $_SESSION['usuario_id'];

// Lógica para salvar a frequência
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['frequencia'])) {
    $id_prof_turma_disc = $_POST['id_prof_turma_disc_hidden'];
    $data_aula = $_POST['data_aula_hidden'];
    $frequencias = $_POST['frequencia'];

    $sql = "INSERT INTO frequencia (id_professor_turma_disciplina, id_aluno_usuario, data_aula, status_presenca) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE status_presenca = VALUES(status_presenca)";
    $stmt = $conexao->prepare($sql);

    foreach ($frequencias as $id_aluno => $status) {
        $stmt->bind_param("iiss", $id_prof_turma_disc, $id_aluno, $data_aula, $status);
        $stmt->execute();
    }
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF'] . "?id_prof_turma_disc=" . $id_prof_turma_disc . "&data_aula=" . $data_aula);
    exit();
}

// Busca as turmas do professor
$sql_alocacoes = "SELECT ptd.id, t.nome AS nome_turma, d.nome AS nome_disciplina FROM professores_turmas_disciplinas ptd JOIN turmas t ON ptd.id_turma = t.id JOIN disciplinas d ON ptd.id_disciplina = d.id WHERE ptd.id_professor_usuario = ? ORDER BY t.nome, d.nome";
$stmt_alocacoes = $conexao->prepare($sql_alocacoes);
$stmt_alocacoes->bind_param("i", $id_professor);
$stmt_alocacoes->execute();
$result_alocacoes = $stmt_alocacoes->get_result();

$alunos_da_turma = [];
$id_prof_turma_disc_selecionado = $_GET['id_prof_turma_disc'] ?? null;
$data_aula_selecionada = $_GET['data_aula'] ?? date('Y-m-d');

if ($id_prof_turma_disc_selecionado) {
    // Busca os alunos da turma selecionada
    $sql_alunos = "SELECT u.id, u.nome FROM usuarios u JOIN alunos_turmas at ON u.id = at.id_aluno_usuario JOIN professores_turmas_disciplinas ptd ON at.id_turma = ptd.id_turma WHERE ptd.id = ? ORDER BY u.nome";
    $stmt_alunos = $conexao->prepare($sql_alunos);
    $stmt_alunos->bind_param("i", $id_prof_turma_disc_selecionado);
    $stmt_alunos->execute();
    $result_alunos = $stmt_alunos->get_result();
    while ($aluno = $result_alunos->fetch_assoc()) {
        $alunos_da_turma[] = $aluno;
    }
    $stmt_alunos->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registrar Frequência</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <div class="content-box">
            <h1>Registrar Frequência</h1>

            <form action="registrar_frequencia.php" method="get" style="margin-bottom: 20px;">
                <div class="form-group">
                    <label for="id_prof_turma_disc">Selecione a Turma/Disciplina:</label>
                    <select id="id_prof_turma_disc" name="id_prof_turma_disc" required>
                        <option value="">-- Selecione --</option>
                        <?php mysqli_data_seek($result_alocacoes, 0); while ($aloc = $result_alocacoes->fetch_assoc()): ?>
                            <option value="<?php echo $aloc['id']; ?>" <?php echo ($id_prof_turma_disc_selecionado == $aloc['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($aloc['nome_turma'] . " - " . $aloc['nome_disciplina']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="data_aula">Data da Aula:</label>
                    <input type="date" id="data_aula" name="data_aula" value="<?php echo $data_aula_selecionada; ?>" required>
                </div>
                <button type="submit" class="btn">Buscar Alunos</button>
            </form>

            <?php if ($id_prof_turma_disc_selecionado && !empty($alunos_da_turma)): ?>
                <hr>
                <form action="registrar_frequencia.php" method="post">
                    <input type="hidden" name="id_prof_turma_disc_hidden" value="<?php echo $id_prof_turma_disc_selecionado; ?>">
                    <input type="hidden" name="data_aula_hidden" value="<?php echo $data_aula_selecionada; ?>">

                    <h3>Lista de Alunos - <?php echo date('d/m/Y', strtotime($data_aula_selecionada)); ?></h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Nome do Aluno</th>
                                <th style="width: 200px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alunos_da_turma as $aluno): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                                    <td>
                                        <label><input type="radio" name="frequencia[<?php echo $aluno['id']; ?>]" value="presente" checked> Presente</label>
                                        <label style="margin-left: 10px;"><input type="radio" name="frequencia[<?php echo $aluno['id']; ?>]" value="ausente"> Ausente</label>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button type="submit" class="btn" style="margin-top: 20px;">Salvar Frequência</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>