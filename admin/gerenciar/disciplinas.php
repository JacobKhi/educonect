<?php
session_start();
// Caminho corrigido para a conexão (sobe dois níveis)
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nome_disciplina'])) {
    $nome_disciplina = $_POST['nome_disciplina'];
    if (!empty($nome_disciplina)) {
        $sql = "INSERT INTO disciplinas (nome) VALUES (?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("s", $nome_disciplina);
        $stmt->execute();
        $stmt->close();
        header("Location: disciplinas.php");
        exit();
    }
}

$sql_busca = "SELECT id, nome FROM disciplinas ORDER BY id ASC";
$resultado_disciplinas = $conexao->query($sql_busca);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Disciplinas</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <div class="content-box">
            <h1>Gerenciar Disciplinas</h1>
            <div class="form-and-list-layout">
                <div class="form-section">
                    <h3>Adicionar Nova Disciplina</h3>
                    <form action="disciplinas.php" method="post">
                        <div class="form-group">
                            <label for="nome">Nome da Disciplina:</label>
                            <input type="text" id="nome" name="nome_disciplina" required>
                        </div>
                        <button type="submit" class="btn">Salvar Disciplina</button>
                    </form>
                </div>
                <div class="list-section">
                    <h3>Disciplinas Cadastradas</h3>
                    <?php if ($resultado_disciplinas->num_rows > 0): ?>
                        <table>
                            <thead><tr><th>ID</th><th>Nome</th></tr></thead>
                            <tbody>
                                <?php while($disciplina = $resultado_disciplinas->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $disciplina['id']; ?></td>
                                        <td><?php echo htmlspecialchars($disciplina['nome']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Nenhuma disciplina cadastrada ainda.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>
<?php $conexao->close(); ?>