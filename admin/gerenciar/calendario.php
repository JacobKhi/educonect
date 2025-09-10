<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

// Bloco de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

// Lógica para INSERIR novo evento
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nome_evento'])) {
    $nome = $_POST['nome_evento'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = !empty($_POST['data_fim']) ? $_POST['data_fim'] : NULL;
    $tipo = $_POST['tipo_evento'];
    $id_usuario_criador = $_SESSION['usuario_id'];

    $sql = "INSERT INTO eventos_calendario (id_usuario_criador, nome, data_inicio, data_fim, tipo) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("issss", $id_usuario_criador, $nome, $data_inicio, $data_fim, $tipo);
    $stmt->execute();
    $stmt->close();

    header("Location: calendario.php");
    exit();
}

// Lógica para EXCLUIR um evento
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id_evento = $_GET['id'];
    $sql_delete = "DELETE FROM eventos_calendario WHERE id = ?";
    $stmt_delete = $conexao->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_evento);
    $stmt_delete->execute();
    $stmt_delete->close();
    header("Location: calendario.php");
    exit();
}


// Busca os eventos já cadastrados
$sql_busca = "SELECT id, nome, data_inicio, data_fim, tipo FROM eventos_calendario ORDER BY data_inicio ASC";
$resultado_eventos = $conexao->query($sql_busca);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Calendário Escolar</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <div class="content-box">
            <h1>Gerenciar Calendário Escolar</h1>
            <div class="form-and-list-layout">
                <div class="form-section">
                    <h3>Adicionar Novo Evento</h3>
                    <form action="calendario.php" method="post">
                        <div class="form-group">
                            <label for="nome_evento">Nome do Evento:</label>
                            <input type="text" id="nome_evento" name="nome_evento" required>
                        </div>
                        <div class="form-group">
                            <label for="tipo_evento">Tipo:</label>
                            <select id="tipo_evento" name="tipo_evento" required>
                                <option value="Feriado">Feriado</option>
                                <option value="Evento Escolar">Evento Escolar</option>
                                <option value="Período de Provas">Período de Provas</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="data_inicio">Data de Início:</label>
                            <input type="date" id="data_inicio" name="data_inicio" required>
                        </div>
                        <div class="form-group">
                            <label for="data_fim">Data de Fim (opcional, para eventos de mais de um dia):</label>
                            <input type="date" id="data_fim" name="data_fim">
                        </div>
                        <button type="submit" class="btn">Adicionar Evento</button>
                    </form>
                </div>
                <div class="list-section">
                    <h3>Eventos Cadastrados</h3>
                    <?php if ($resultado_eventos && $resultado_eventos->num_rows > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Evento</th>
                                    <th>Tipo</th>
                                    <th>Período</th>
                                    <th style="width: 120px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($evento = $resultado_eventos->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($evento['nome']); ?></td>
                                        <td><?php echo htmlspecialchars($evento['tipo']); ?></td>
                                        <td>
                                            <?php 
                                                echo date('d/m/Y', strtotime($evento['data_inicio'])); 
                                                if ($evento['data_fim']) {
                                                    echo " a " . date('d/m/Y', strtotime($evento['data_fim']));
                                                }
                                            ?>
                                        </td>
                                        <td class="actions-cell">
                                            <a href="calendario.php?acao=excluir&id=<?php echo $evento['id']; ?>" class="btn btn-small btn-delete" onclick="return confirm('Tem certeza que deseja excluir este evento?')">Excluir</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Nenhum evento cadastrado ainda.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>
<?php $conexao->close(); ?>