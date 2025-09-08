<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nome_turma'])) {
    $nome_turma = $_POST['nome_turma'];
    $ano_letivo = $_POST['ano_letivo'];
    if (!empty($nome_turma) && !empty($ano_letivo)) {
        $sql = "INSERT INTO turmas (nome, ano_letivo) VALUES (?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("si", $nome_turma, $ano_letivo);
        $stmt->execute();
        $stmt->close();
        header("Location: turmas.php");
        exit();
    }
}

$sql_busca = "SELECT id, nome, ano_letivo FROM turmas ORDER BY ano_letivo DESC, nome ASC";
$resultado_turmas = $conexao->query($sql_busca);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Turmas</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <div class="content-box">
            <h1>Gerenciar Turmas</h1>
            <div class="form-and-list-layout">
                <div class="form-section">
                    <h3>Adicionar Nova Turma</h3>
                    <form action="turmas.php" method="post">
                        <div class="form-group">
                            <label for="nome_turma">Nome da Turma (ex: Turma 601):</label>
                            <input type="text" id="nome_turma" name="nome_turma" required>
                        </div>
                        <div class="form-group">
                            <label for="ano_letivo">Ano Letivo:</label>
                            <input type="number" id="ano_letivo" name="ano_letivo" value="<?php echo date('Y'); ?>" required>
                        </div>
                        <button type="submit" class="btn">Salvar Turma</button>
                    </form>
                </div>
                <div class="list-section">
                    <h3>Turmas Cadastradas</h3>
                    <?php if ($resultado_turmas->num_rows > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Ano Letivo</th>
                                    <th style="width: 150px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($turma = $resultado_turmas->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $turma['id']; ?></td>
                                        <td><?php echo htmlspecialchars($turma['nome']); ?></td>
                                        <td><?php echo htmlspecialchars($turma['ano_letivo']); ?></td>
                                        <td class="actions-cell">
                                            <a href="editar_turma.php?id=<?php echo $turma['id']; ?>" class="btn btn-small btn-edit">Editar</a>
                                            <a href="excluir_turma.php?id=<?php echo $turma['id']; ?>" class="btn btn-small btn-delete">Excluir</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Nenhuma turma cadastrada ainda.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>
<?php $conexao->close(); ?>