<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

// Bloco de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_professor'])) {
    $id_professor = $_POST['id_professor'];
    $id_turma = $_POST['id_turma'];
    $id_disciplina = $_POST['id_disciplina'];

    $sql = "INSERT INTO professores_turmas_disciplinas (id_professor_usuario, id_turma, id_disciplina) VALUES (?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $id_professor, $id_turma, $id_disciplina);
    
    $stmt->execute();
    $stmt->close();
    
    header("Location: alocar_professores.php");
    exit();
}

$sql_professores = "SELECT id, nome FROM usuarios WHERE id_perfil = 2 ORDER BY nome ASC";
$result_professores = $conexao->query($sql_professores);

$sql_turmas = "SELECT id, nome FROM turmas ORDER BY nome ASC";
$result_turmas = $conexao->query($sql_turmas);

$sql_disciplinas = "SELECT id, nome FROM disciplinas ORDER BY nome ASC";
$result_disciplinas = $conexao->query($sql_disciplinas);

// Busca as alocações e agrupa por turma
$sql_alocacoes = "
    SELECT 
        ptd.id, 
        u.nome AS nome_professor,
        t.nome AS nome_turma,
        d.nome AS nome_disciplina
    FROM 
        professores_turmas_disciplinas ptd
    JOIN usuarios u ON ptd.id_professor_usuario = u.id
    JOIN turmas t ON ptd.id_turma = t.id
    JOIN disciplinas d ON ptd.id_disciplina = d.id
    ORDER BY nome_turma, nome_disciplina, nome_professor;
";
$result_alocacoes = $conexao->query($sql_alocacoes);

$alocacoes_por_turma = [];
if ($result_alocacoes) {
    while ($aloc = $result_alocacoes->fetch_assoc()) {
        $alocacoes_por_turma[$aloc['nome_turma']][] = $aloc;
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Alocar Professores</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>

    <div class="container">
        <div class="content-box">
            <h1>Alocar Professores a Turmas/Disciplinas</h1>
            <div class="form-and-list-layout">
                <div class="form-section">
                    <h3>Nova Alocação</h3>
                    <form action="alocar_professores.php" method="post">
                        <div class="form-group">
                            <label for="id_professor">Professor:</label>
                            <select id="id_professor" name="id_professor" required>
                                <option value="">-- Selecione --</option>
                                <?php while($prof = $result_professores->fetch_assoc()): ?>
                                    <option value="<?php echo $prof['id']; ?>"><?php echo htmlspecialchars($prof['nome']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_turma">Turma:</label>
                            <select id="id_turma" name="id_turma" required>
                                <option value="">-- Selecione --</option>
                                <?php while($turma = $result_turmas->fetch_assoc()): ?>
                                    <option value="<?php echo $turma['id']; ?>"><?php echo htmlspecialchars($turma['nome']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_disciplina">Disciplina:</label>
                            <select id="id_disciplina" name="id_disciplina" required>
                                <option value="">-- Selecione --</option>
                                <?php while($disc = $result_disciplinas->fetch_assoc()): ?>
                                    <option value="<?php echo $disc['id']; ?>"><?php echo htmlspecialchars($disc['nome']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn">Salvar Alocação</button>
                    </form>
                </div>

                <div class="list-section">
                    <h3>Alocações Existentes</h3>
                    <?php if (!empty($alocacoes_por_turma)): ?>
                        <?php foreach ($alocacoes_por_turma as $nome_turma => $alocacoes): ?>
                            <details class="accordion-item">
                                <summary class="accordion-header"><?php echo htmlspecialchars($nome_turma); ?></summary>
                                <div class="accordion-content">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Disciplina</th>
                                                <th>Professor Responsável</th>
                                                <th style="width: 120px;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($alocacoes as $aloc): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($aloc['nome_disciplina']); ?></td>
                                                    <td><?php echo htmlspecialchars($aloc['nome_professor']); ?></td>
                                                    <td class="actions-cell">
                                                        <a href="excluir_alocacao.php?id=<?php echo $aloc['id']; ?>" class="btn btn-small btn-delete">Excluir</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Nenhuma alocação cadastrada ainda.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>
<?php $conexao->close(); ?>