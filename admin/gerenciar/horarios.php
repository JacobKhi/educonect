<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

// Bloco de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

// Lógica para INSERIR novo horário
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_prof_turma_disc'])) {
    $id_prof_turma_disc = $_POST['id_prof_turma_disc'];
    $dia_semana = $_POST['dia_semana'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fim = $_POST['hora_fim'];

    $sql = "INSERT INTO horarios (id_prof_turma_disc, dia_semana, hora_inicio, hora_fim) VALUES (?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("isss", $id_prof_turma_disc, $dia_semana, $hora_inicio, $hora_fim);
    $stmt->execute();
    $stmt->close();

    header("Location: horarios.php");
    exit();
}

// Lógica para EXCLUIR um horário
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id_horario = $_GET['id'];
    $sql_delete = "DELETE FROM horarios WHERE id = ?";
    $stmt_delete = $conexao->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_horario);
    $stmt_delete->execute();
    $stmt_delete->close();
    header("Location: horarios.php");
    exit();
}

// Busca todas as alocações (Turma/Disciplina/Professor) para o formulário
$sql_alocacoes = "
    SELECT ptd.id, t.nome AS nome_turma, d.nome AS nome_disciplina, u.nome AS nome_professor
    FROM professores_turmas_disciplinas ptd
    JOIN turmas t ON ptd.id_turma = t.id
    JOIN disciplinas d ON ptd.id_disciplina = d.id
    JOIN usuarios u ON ptd.id_professor_usuario = u.id
    ORDER BY t.nome, d.nome;
";
$result_alocacoes = $conexao->query($sql_alocacoes);

// Busca os horários já cadastrados e agrupa por turma
$sql_horarios = "
    SELECT 
        h.id, 
        t.nome AS nome_turma,
        d.nome AS nome_disciplina,
        h.dia_semana,
        h.hora_inicio,
        h.hora_fim
    FROM horarios h
    JOIN professores_turmas_disciplinas ptd ON h.id_prof_turma_disc = ptd.id
    JOIN turmas t ON ptd.id_turma = t.id
    JOIN disciplinas d ON ptd.id_disciplina = d.id
    ORDER BY t.nome, h.dia_semana, h.hora_inicio;
";
$result_horarios = $conexao->query($sql_horarios);

$horarios_por_turma = [];
if ($result_horarios) {
    while ($horario = $result_horarios->fetch_assoc()) {
        $horarios_por_turma[$horario['nome_turma']][] = $horario;
    }
}

$dias_da_semana = [1 => 'Segunda-feira', 2 => 'Terça-feira', 3 => 'Quarta-feira', 4 => 'Quinta-feira', 5 => 'Sexta-feira', 6 => 'Sábado', 7 => 'Domingo'];

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Horários de Aulas</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <div class="content-box">
            <h1>Gerenciar Horários de Aulas</h1>
            <div class="form-and-list-layout">
                <div class="form-section">
                    <h3>Adicionar Novo Horário</h3>
                    <form action="horarios.php" method="post">
                        <div class="form-group">
                            <label for="id_prof_turma_disc">Turma / Disciplina:</label>
                            <select id="id_prof_turma_disc" name="id_prof_turma_disc" required>
                                <option value="">-- Selecione --</option>
                                <?php while($aloc = $result_alocacoes->fetch_assoc()): ?>
                                    <option value="<?php echo $aloc['id']; ?>"><?php echo htmlspecialchars($aloc['nome_turma'] . " - " . $aloc['nome_disciplina']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="dia_semana">Dia da Semana:</label>
                            <select id="dia_semana" name="dia_semana" required>
                                <?php foreach($dias_da_semana as $num => $dia): ?>
                                    <option value="<?php echo $num; ?>"><?php echo $dia; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="hora_inicio">Hora de Início:</label>
                            <input type="time" id="hora_inicio" name="hora_inicio" required>
                        </div>
                        <div class="form-group">
                            <label for="hora_fim">Hora de Fim:</label>
                            <input type="time" id="hora_fim" name="hora_fim" required>
                        </div>
                        <button type="submit" class="btn">Adicionar Horário</button>
                    </form>
                </div>
                <div class="list-section">
                    <h3>Horários Cadastrados</h3>
                     <?php if (!empty($horarios_por_turma)): ?>
                        <?php foreach ($horarios_por_turma as $nome_turma => $horarios): ?>
                            <details class="accordion-item" open>
                                <summary class="accordion-header"><?php echo htmlspecialchars($nome_turma); ?></summary>
                                <div class="accordion-content">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Dia</th>
                                                <th>Horário</th>
                                                <th>Disciplina</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($horarios as $horario): ?>
                                                <tr>
                                                    <td><?php echo $dias_da_semana[$horario['dia_semana']]; ?></td>
                                                    <td><?php echo date('H:i', strtotime($horario['hora_inicio'])) . ' - ' . date('H:i', strtotime($horario['hora_fim'])); ?></td>
                                                    <td><?php echo htmlspecialchars($horario['nome_disciplina']); ?></td>
                                                    <td class="actions-cell">
                                                        <a href="horarios.php?acao=excluir&id=<?php echo $horario['id']; ?>" class="btn btn-small btn-delete" onclick="return confirm('Tem certeza?')">Excluir</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Nenhum horário cadastrado ainda.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>
<?php $conexao->close(); ?>