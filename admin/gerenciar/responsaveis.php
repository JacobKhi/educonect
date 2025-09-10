<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

// Bloco de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

// Lógica para vincular um responsável a um aluno
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_aluno'])) {
    $id_aluno = $_POST['id_aluno'];
    $id_responsavel = $_POST['id_responsavel'];

    if ($id_aluno && $id_responsavel) {
        // Verificar se o vínculo já existe
        $check_sql = "SELECT * FROM responsaveis_alunos WHERE id_aluno_usuario = ? AND id_responsavel_usuario = ?";
        $check_stmt = $conexao->prepare($check_sql);
        $check_stmt->bind_param("ii", $id_aluno, $id_responsavel);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows == 0) {
            $insert_sql = "INSERT INTO responsaveis_alunos (id_aluno_usuario, id_responsavel_usuario) VALUES (?, ?)";
            $insert_stmt = $conexao->prepare($insert_sql);
            $insert_stmt->bind_param("ii", $id_aluno, $id_responsavel);
            $insert_stmt->execute();
            $insert_stmt->close();
        }
        $check_stmt->close();
    }
    header("Location: gerenciar_responsaveis.php");
    exit();
}

// Lógica para desvincular (excluir)
if (isset($_GET['acao']) && $_GET['acao'] == 'desvincular' && isset($_GET['id_aluno']) && isset($_GET['id_responsavel'])) {
    $id_aluno = $_GET['id_aluno'];
    $id_responsavel = $_GET['id_responsavel'];
    
    $delete_sql = "DELETE FROM responsaveis_alunos WHERE id_aluno_usuario = ? AND id_responsavel_usuario = ?";
    $delete_stmt = $conexao->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $id_aluno, $id_responsavel);
    $delete_stmt->execute();
    $delete_stmt->close();

    header("Location: gerenciar_responsaveis.php");
    exit();
}

// Buscar todos os alunos e responsáveis ativos
$sql_alunos = "SELECT id, nome FROM usuarios WHERE id_perfil = 1 ORDER BY nome ASC";
$result_alunos = $conexao->query($sql_alunos);

$sql_responsaveis = "SELECT id, nome FROM usuarios WHERE id_perfil = 3 ORDER BY nome ASC";
$result_responsaveis = $conexao->query($sql_responsaveis);

// Buscar todos os vínculos existentes e agrupar por aluno
$sql_vinculos = "
    SELECT 
        ra.id_aluno_usuario, 
        ra.id_responsavel_usuario,
        aluno.nome AS nome_aluno,
        responsavel.nome AS nome_responsavel
    FROM responsaveis_alunos ra
    JOIN usuarios aluno ON ra.id_aluno_usuario = aluno.id
    JOIN usuarios responsavel ON ra.id_responsavel_usuario = responsavel.id
    ORDER BY aluno.nome, responsavel.nome;
";
$result_vinculos = $conexao->query($sql_vinculos);

$vinculos_por_aluno = [];
if ($result_vinculos) {
    while ($vinculo = $result_vinculos->fetch_assoc()) {
        $vinculos_por_aluno[$vinculo['nome_aluno']][] = $vinculo;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Responsáveis</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>

    <div class="container">
        <div class="content-box">
            <h1>Gerenciar Vínculos de Responsáveis</h1>
            <div class="form-and-list-layout">
                <div class="form-section">
                    <h3>Vincular Responsável a Aluno</h3>
                    <form action="gerenciar_responsaveis.php" method="post">
                        <div class="form-group">
                            <label for="id_aluno">Selecione o Aluno:</label>
                            <select id="id_aluno" name="id_aluno" required>
                                <option value="">-- Selecione --</option>
                                <?php mysqli_data_seek($result_alunos, 0); while($aluno = $result_alunos->fetch_assoc()): ?>
                                    <option value="<?php echo $aluno['id']; ?>"><?php echo htmlspecialchars($aluno['nome']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_responsavel">Selecione o Responsável:</label>
                            <select id="id_responsavel" name="id_responsavel" required>
                                <option value="">-- Selecione --</option>
                                <?php while($responsavel = $result_responsaveis->fetch_assoc()): ?>
                                    <option value="<?php echo $responsavel['id']; ?>"><?php echo htmlspecialchars($responsavel['nome']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn">Vincular</button>
                    </form>
                </div>

                <div class="list-section">
                    <h3>Vínculos Atuais</h3>
                    <?php if (!empty($vinculos_por_aluno)): ?>
                        <?php foreach ($vinculos_por_aluno as $nome_aluno => $vinculos): ?>
                            <details class="accordion-item">
                                <summary class="accordion-header"><?php echo htmlspecialchars($nome_aluno); ?> (<?php echo count($vinculos); ?> Responsáveis)</summary>
                                <div class="accordion-content">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Responsável</th>
                                                <th style="width: 120px;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($vinculos as $vinculo): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($vinculo['nome_responsavel']); ?></td>
                                                    <td class="actions-cell">
                                                        <a href="?acao=desvincular&id_aluno=<?php echo $vinculo['id_aluno_usuario']; ?>&id_responsavel=<?php echo $vinculo['id_responsavel_usuario']; ?>" class="btn btn-small btn-delete">Desvincular</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Nenhum vínculo cadastrado ainda.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>