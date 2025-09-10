<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/conexao.php';

// Redireciona se não houver ninguém logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

$id_aluno_alvo = 0;
$nome_aluno_alvo = "";

// Lógica para determinar de qual aluno mostrar os dados
if ($_SESSION['usuario_perfil_id'] == 1) { // Se for o próprio ALUNO a ver
    $id_aluno_alvo = $_SESSION['usuario_id'];
    $nome_aluno_alvo = htmlspecialchars($_SESSION['usuario_nome']);
} elseif ($_SESSION['usuario_perfil_id'] == 3 && isset($_GET['id_aluno'])) { // Se for um RESPONSÁVEL
    $id_aluno_alvo = $_GET['id_aluno'];
    
    // Verificação de segurança: O responsável tem permissão para ver este aluno?
    $sql_check = "SELECT u.nome FROM responsaveis_alunos ra JOIN usuarios u ON ra.id_aluno_usuario = u.id WHERE ra.id_aluno_usuario = ? AND ra.id_responsavel_usuario = ?";
    $stmt_check = $conexao->prepare($sql_check);
    $stmt_check->bind_param("ii", $id_aluno_alvo, $_SESSION['usuario_id']);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows == 0) {
        die("Acesso não autorizado."); // Medida de segurança
    }
    $aluno_info = $result_check->fetch_assoc();
    $nome_aluno_alvo = htmlspecialchars($aluno_info['nome']);
    $stmt_check->close();
} else {
    die("Acesso não autorizado.");
}

// Buscar todas as notas do aluno, com detalhes da atividade e disciplina
$sql_notas = "
    SELECT 
        d.nome AS nome_disciplina,
        a.titulo AS titulo_atividade,
        a.data_entrega,
        n.valor_nota
    FROM notas n
    JOIN atividades a ON n.id_atividade = a.id
    JOIN professores_turmas_disciplinas ptd ON a.id_professor_turma_disciplina = ptd.id
    JOIN disciplinas d ON ptd.id_disciplina = d.id
    WHERE n.id_aluno_usuario = ? 
    ORDER BY d.nome ASC, a.data_entrega DESC
";
$stmt = $conexao->prepare($sql_notas);
$stmt->bind_param("i", $id_aluno_alvo);
$stmt->execute();
$result_notas = $stmt->get_result();
$notas_por_disciplina = [];
while ($row = $result_notas->fetch_assoc()) {
    $notas_por_disciplina[$row['nome_disciplina']][] = $row;
}
$stmt->close();
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minhas Notas</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <div class="content-box">
            <h1>Minhas Notas</h1>
            <p>Acompanhe seu desempenho em todas as disciplinas.</p>
            <hr>

            <?php if (empty($notas_por_disciplina)): ?>
                <p style="text-align: center; margin-top: 20px;">Nenhuma nota foi lançada para você ainda.</p>
            <?php else: ?>
                <?php foreach ($notas_por_disciplina as $disciplina => $notas): ?>
                    <h3 style="margin-top: 30px; color: #1E3A8A;"><?php echo htmlspecialchars($disciplina); ?></h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Atividade</th>
                                <th>Data de Entrega</th>
                                <th style="width: 150px;">Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notas as $nota): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($nota['titulo_atividade']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($nota['data_entrega'])); ?></td>
                                    <td><strong><?php echo htmlspecialchars(number_format($nota['valor_nota'], 2, ',', '.')); ?></strong></td>
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