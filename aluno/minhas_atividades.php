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

// Buscar todas as atividades do aluno, organizadas por disciplina
$sql_atividades = "
    SELECT 
        d.nome AS nome_disciplina,
        a.titulo,
        a.instrucoes,
        a.data_entrega
    FROM atividades a
    JOIN professores_turmas_disciplinas ptd ON a.id_professor_turma_disciplina = ptd.id
    JOIN disciplinas d ON ptd.id_disciplina = d.id
    JOIN alunos_turmas at ON ptd.id_turma = at.id_turma
    WHERE at.id_aluno_usuario = ?
    ORDER BY d.nome ASC, a.data_entrega DESC
";
$stmt = $conexao->prepare($sql_atividades);
$stmt->bind_param("i", $id_aluno);
$stmt->execute();
$result_atividades = $stmt->get_result();
$atividades_por_disciplina = [];
while ($row = $result_atividades->fetch_assoc()) {
    $atividades_por_disciplina[$row['nome_disciplina']][] = $row;
}
$stmt->close();
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minhas Atividades</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <div class="content-box">
            <h1>Minhas Atividades</h1>
            <p>Veja todas as suas tarefas e trabalhos pendentes e concluídos.</p>
            <hr>

            <?php if (empty($atividades_por_disciplina)): ?>
                <p style="text-align: center; margin-top: 20px;">Nenhuma atividade foi criada para você ainda.</p>
            <?php else: ?>
                <?php foreach ($atividades_por_disciplina as $disciplina => $atividades): ?>
                    <h3 style="margin-top: 30px; color: #1E3A8A;"><?php echo htmlspecialchars($disciplina); ?></h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Atividade</th>
                                <th>Instruções</th>
                                <th style="width: 150px;">Data de Entrega</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($atividades as $atividade): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($atividade['titulo']); ?></td>
                                    <td><?php echo htmlspecialchars($atividade['instrucoes']); ?></td>
                                    <td><strong><?php echo date('d/m/Y', strtotime($atividade['data_entrega'])); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>