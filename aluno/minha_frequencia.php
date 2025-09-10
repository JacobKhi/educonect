<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/conexao.php';

// Redireciona se não for um aluno logado
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 1) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

$id_aluno = $_SESSION['usuario_id'];

// Buscar todos os registos de frequência do aluno
$sql_frequencia = "
    SELECT 
        d.nome AS nome_disciplina,
        f.data_aula,
        f.status_presenca
    FROM frequencia f
    JOIN professores_turmas_disciplinas ptd ON f.id_professor_turma_disciplina = ptd.id
    JOIN disciplinas d ON ptd.id_disciplina = d.id
    WHERE f.id_aluno_usuario = ? 
    ORDER BY d.nome ASC, f.data_aula DESC
";
$stmt = $conexao->prepare($sql_frequencia);
$stmt->bind_param("i", $id_aluno);
$stmt->execute();
$result_frequencia = $stmt->get_result();

// Agrupar os resultados por disciplina
$frequencia_por_disciplina = [];
while ($row = $result_frequencia->fetch_assoc()) {
    $frequencia_por_disciplina[$row['nome_disciplina']][] = $row;
}
$stmt->close();
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minha Frequência</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
    <style>
        .status-presente { color: #10B981; font-weight: bold; }
        .status-ausente { color: #ef4444; font-weight: bold; }
    </style>
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <div class="content-box">
            <h1>Minha Frequência</h1>
            <p>Acompanhe o seu registo de presenças e ausências em cada disciplina.</p>
            <hr>

            <?php if (empty($frequencia_por_disciplina)): ?>
                <p style="text-align: center; margin-top: 20px;">Nenhum registo de frequência encontrado.</p>
            <?php else: ?>
                <?php foreach ($frequencia_por_disciplina as $disciplina => $frequencias): ?>
                    <?php
                        // Calcular o total de ausências para esta disciplina
                        $total_ausencias = 0;
                        foreach ($frequencias as $frequencia) {
                            if ($frequencia['status_presenca'] == 'ausente') {
                                $total_ausencias++;
                            }
                        }
                    ?>
                    <details class="accordion-item" open>
                        <summary class="accordion-header">
                            <?php echo htmlspecialchars($disciplina); ?>
                            <span style="font-weight: normal; color: #ef4444;">(Total de Faltas: <?php echo $total_ausencias; ?>)</span>
                        </summary>
                        <div class="accordion-content">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Data da Aula</th>
                                        <th style="width: 150px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($frequencias as $frequencia): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($frequencia['data_aula'])); ?></td>
                                            <td>
                                                <?php if ($frequencia['status_presenca'] == 'presente'): ?>
                                                    <span class="status-presente">Presente</span>
                                                <?php else: ?>
                                                    <span class="status-ausente">Ausente</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </details>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>