<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 2) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

$id_professor = $_SESSION['usuario_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['titulo'])) {
    $id_prof_turma_disc = $_POST['id_prof_turma_disc'];
    $titulo = $_POST['titulo'];
    $instrucoes = $_POST['instrucoes'];
    $data_entrega = $_POST['data_entrega'];

    if (!empty($id_prof_turma_disc) && !empty($titulo)) {
        $sql = "INSERT INTO atividades (id_prof_turma_disc, titulo, instrucoes, data_entrega) VALUES (?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("isss", $id_prof_turma_disc, $titulo, $instrucoes, $data_entrega);
        $stmt->execute();
        $stmt->close();
        
        header("Location: atividades.php");
        exit();
    }
}

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

$sql_atividades = "
    SELECT a.titulo, a.data_entrega, t.nome AS nome_turma, d.nome AS nome_disciplina
    FROM atividades a
    JOIN professores_turmas_disciplinas ptd ON a.id_professor_turma_disciplina = ptd.id
    JOIN turmas t ON ptd.id_turma = t.id
    JOIN disciplinas d ON ptd.id_disciplina = d.id
    WHERE ptd.id_professor_usuario = ?
    ORDER BY a.data_entrega DESC
";
$stmt_atividades = $conexao->prepare($sql_atividades);
$stmt_atividades->bind_param("i", $id_professor);
$stmt_atividades->execute();
$result_atividades = $stmt_atividades->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Atividades</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <div class="content-box">
            <h1>Gerenciar Atividades</h1>
            <div class="form-and-list-layout">
                <div class="form-section">
                    <h3>Criar Nova Atividade</h3>
                    <form action="atividades.php" method="post">
                        <div class="form-group">
                            <label for="id_prof_turma_disc">Para a Turma/Disciplina:</label>
                            <select id="id_prof_turma_disc" name="id_prof_turma_disc" required style="width: 100%; padding: 8px; margin-bottom: 15px;">
                                <option value="">-- Selecione --</option>
                                <?php if ($result_alocacoes->num_rows > 0): ?>
                                    <?php while($aloc = $result_alocacoes->fetch_assoc()): ?>
                                        <option value="<?php echo $aloc['id']; ?>">
                                            <?php echo htmlspecialchars($aloc['nome_turma'] . " - " . $aloc['nome_disciplina']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="titulo">Título da Atividade:</label>
                            <input type="text" id="titulo" name="titulo" required>
                        </div>
                        <div class="form-group">
                            <label for="instrucoes">Instruções (opcional):</label>
                            <textarea id="instrucoes" name="instrucoes" rows="4" style="width: 100%; padding: 8px; box-sizing: border-box;"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="data_entrega">Data de Entrega:</label>
                            <input type="date" id="data_entrega" name="data_entrega">
                        </div>
                        <button type="submit" class="btn">Salvar Atividade</button>
                    </form>
                </div>
                <div class="list-section">
                    <h3>Atividades Criadas</h3>
                    <?php if ($result_atividades && $result_atividades->num_rows > 0): ?>
                        <table>
                            <thead><tr><th>Título</th><th>Turma/Disciplina</th><th>Data de Entrega</th></tr></thead>
                            <tbody>
                                <?php while($ativ = $result_atividades->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ativ['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($ativ['nome_turma'] . " - " . $ativ['nome_disciplina']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($ativ['data_entrega'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Nenhuma atividade criada ainda.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>