<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/conexao.php';

// Bloco de segurança: apenas professores
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 2) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

$id_professor = $_SESSION['usuario_id'];

// Lógica para salvar as notas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['notas'])) {
    $id_atividade = $_POST['id_atividade'];
    $notas = $_POST['notas'];

    $sql = "INSERT INTO notas (id_atividade, id_aluno_usuario, valor_nota) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE valor_nota = VALUES(valor_nota)";
    $stmt = $conexao->prepare($sql);

    foreach ($notas as $id_aluno => $valor_nota) {
        if (!empty($valor_nota)) {
            $stmt->bind_param("iid", $id_atividade, $id_aluno, $valor_nota);
            $stmt->execute();
        }
    }
    $stmt->close();
    // Redireciona para a mesma página para evitar reenvio do formulário
    header("Location: " . $_SERVER['PHP_SELF'] . "?id_prof_turma_disc=" . $_POST['id_prof_turma_disc_hidden']);
    exit();
}


// Busca as turmas e disciplinas do professor
$sql_alocacoes = "
    SELECT ptd.id, t.nome AS nome_turma, d.nome AS nome_disciplina
    FROM professores_turmas_disciplinas ptd
    JOIN turmas t ON ptd.id_turma = t.id
    JOIN disciplinas d ON ptd.id_disciplina = d.id
    WHERE ptd.id_professor_usuario = ?
    ORDER BY t.nome, d.nome
";
$stmt_alocacoes = $conexao->prepare($sql_alocacoes);
$stmt_alocacoes->bind_param("i", $id_professor);
$stmt_alocacoes->execute();
$result_alocacoes = $stmt_alocacoes->get_result();

$alunos_da_turma = [];
$atividades_da_turma = [];
$id_prof_turma_disc_selecionado = $_GET['id_prof_turma_disc'] ?? null;

if ($id_prof_turma_disc_selecionado) {
    // Busca os alunos da turma selecionada
    $sql_alunos = "
        SELECT u.id, u.nome 
        FROM usuarios u
        JOIN alunos_turmas at ON u.id = at.id_aluno_usuario
        JOIN professores_turmas_disciplinas ptd ON at.id_turma = ptd.id_turma
        WHERE ptd.id = ?
        ORDER BY u.nome
    ";
    $stmt_alunos = $conexao->prepare($sql_alunos);
    $stmt_alunos->bind_param("i", $id_prof_turma_disc_selecionado);
    $stmt_alunos->execute();
    $result_alunos = $stmt_alunos->get_result();
    while ($aluno = $result_alunos->fetch_assoc()) {
        $alunos_da_turma[] = $aluno;
    }
    $stmt_alunos->close();

    // Busca as atividades da turma selecionada
    $sql_atividades = "SELECT id, titulo FROM atividades WHERE id_professor_turma_disciplina = ? ORDER BY titulo";
    $stmt_atividades = $conexao->prepare($sql_atividades);
    $stmt_atividades->bind_param("i", $id_prof_turma_disc_selecionado);
    $stmt_atividades->execute();
    $result_atividades = $stmt_atividades->get_result();
    while ($atividade = $result_atividades->fetch_assoc()) {
        $atividades_da_turma[] = $atividade;
    }
    $stmt_atividades->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lançar Notas</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <div class="content-box">
            <h1>Lançar Notas</h1>

            <form action="lancar_notas.php" method="get" style="margin-bottom: 20px;">
                <div class="form-group">
                    <label for="id_prof_turma_disc">Selecione a Turma/Disciplina:</label>
                    <select id="id_prof_turma_disc" name="id_prof_turma_disc" onchange="this.form.submit()" required>
                        <option value="">-- Selecione --</option>
                        <?php while ($aloc = $result_alocacoes->fetch_assoc()): ?>
                            <option value="<?php echo $aloc['id']; ?>" <?php echo ($id_prof_turma_disc_selecionado == $aloc['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($aloc['nome_turma'] . " - " . $aloc['nome_disciplina']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </form>

            <?php if ($id_prof_turma_disc_selecionado && !empty($alunos_da_turma)): ?>
                <hr>
                <form action="lancar_notas.php" method="post">
                    <input type="hidden" name="id_prof_turma_disc_hidden" value="<?php echo $id_prof_turma_disc_selecionado; ?>">
                    
                    <div class="form-group" style="margin-top: 20px;">
                        <label for="id_atividade">Selecione a Atividade:</label>
                        <select id="id_atividade" name="id_atividade" required>
                            <option value="">-- Selecione uma atividade --</option>
                            <?php foreach ($atividades_da_turma as $atividade): ?>
                                <option value="<?php echo $atividade['id']; ?>">
                                    <?php echo htmlspecialchars($atividade['titulo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <h3>Alunos</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Nome do Aluno</th>
                                <th style="width: 150px;">Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alunos_da_turma as $aluno): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                                    <td>
                                        <input type="number" step="0.01" name="notas[<?php echo $aluno['id']; ?>]" class="form-control" style="padding: 5px; width: 100px;">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button type="submit" class="btn" style="margin-top: 20px;">Salvar Notas</button>
                </form>
            <?php elseif ($id_prof_turma_disc_selecionado): ?>
                <p style="text-align: center; margin-top: 20px;">Nenhum aluno encontrado para esta turma.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>