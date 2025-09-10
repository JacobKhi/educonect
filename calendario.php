<?php
session_start();
require_once __DIR__ . '/config.php';
require_once BASE_PATH . '/conexao.php';

// Redireciona se não houver ninguém logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

// Busca os eventos que estão a decorrer HOJE
$sql_hoje = "SELECT id, nome, data_inicio, data_fim, tipo 
             FROM eventos_calendario 
             WHERE CURDATE() BETWEEN data_inicio AND IFNULL(data_fim, data_inicio)
             ORDER BY data_inicio ASC";
$resultado_eventos_hoje = $conexao->query($sql_hoje);

// Busca os eventos futuros (a partir de AMANHÃ)
$sql_futuros = "SELECT id, nome, data_inicio, data_fim, tipo 
                FROM eventos_calendario 
                WHERE data_inicio > CURDATE() 
                ORDER BY data_inicio ASC";
$resultado_eventos_futuros = $conexao->query($sql_futuros);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Calendário Escolar</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <div class="content-box">
            <h1>Calendário Escolar</h1>
            <p>Fique por dentro dos próximos eventos, feriados e períodos de avaliação.</p>
            <hr>

            <div class="list-section" style="flex: 1; min-width: 100%; margin-bottom: 30px;">
                <h3>Eventos em Andamento (Hoje)</h3>
                <?php if ($resultado_eventos_hoje && $resultado_eventos_hoje->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Evento</th>
                                <th>Tipo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($evento = $resultado_eventos_hoje->fetch_assoc()): ?>
                                <tr>
                                    <td style="width: 200px;">
                                        <strong>
                                            <?php 
                                                echo date('d/m/Y', strtotime($evento['data_inicio'])); 
                                                if ($evento['data_fim'] && $evento['data_fim'] != $evento['data_inicio']) {
                                                    echo " a " . date('d/m/Y', strtotime($evento['data_fim']));
                                                }
                                            ?>
                                        </strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($evento['nome']); ?></td>
                                    <td style="width: 150px;"><?php echo htmlspecialchars($evento['tipo']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Nenhum evento agendado para hoje.</p>
                <?php endif; ?>
            </div>

            <div class="list-section" style="flex: 1; min-width: 100%;">
                <h3>Próximos Eventos</h3>
                <?php if ($resultado_eventos_futuros && $resultado_eventos_futuros->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Evento</th>
                                <th>Tipo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($evento = $resultado_eventos_futuros->fetch_assoc()): ?>
                                <tr>
                                    <td style="width: 200px;">
                                        <strong>
                                            <?php 
                                                echo date('d/m/Y', strtotime($evento['data_inicio'])); 
                                                if ($evento['data_fim']) {
                                                    echo " a " . date('d/m/Y', strtotime($evento['data_fim']));
                                                }
                                            ?>
                                        </strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($evento['nome']); ?></td>
                                    <td style="width: 150px;"><?php echo htmlspecialchars($evento['tipo']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Nenhum evento futuro agendado no momento.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>
<?php $conexao->close(); ?>