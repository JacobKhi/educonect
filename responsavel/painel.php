<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/conexao.php';

// Bloco de segurança: apenas responsáveis (perfil 3) podem aceder
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 3) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

$id_responsavel = $_SESSION['usuario_id'];
$nome_responsavel = htmlspecialchars($_SESSION['usuario_nome']);

// 1. Buscar os filhos (alunos) vinculados a este responsável
$sql_filhos = "
    SELECT u.id, u.nome 
    FROM usuarios u
    JOIN responsaveis_alunos ra ON u.id = ra.id_aluno_usuario
    WHERE ra.id_responsavel_usuario = ?
    ORDER BY u.nome ASC
";
$stmt_filhos = $conexao->prepare($sql_filhos);
$stmt_filhos->bind_param("i", $id_responsavel);
$stmt_filhos->execute();
$result_filhos = $stmt_filhos->get_result();
$filhos = $result_filhos->fetch_all(MYSQLI_ASSOC);
$stmt_filhos->close();

// 2. Para cada filho, buscar os seus dados (notas e atividades)
$dados_filhos = [];
foreach ($filhos as $filho) {
    $id_aluno = $filho['id'];

    // Buscar últimas notas do filho
    $sql_notas = "
        SELECT n.valor_nota, d.nome AS nome_disciplina 
        FROM notas n
        JOIN atividades a ON n.id_atividade = a.id
        JOIN professores_turmas_disciplinas ptd ON a.id_professor_turma_disciplina = ptd.id
        JOIN disciplinas d ON ptd.id_disciplina = d.id
        WHERE n.id_aluno_usuario = ? 
        ORDER BY n.data_lancamento DESC 
        LIMIT 5
    ";
    $stmt_notas = $conexao->prepare($sql_notas);
    $stmt_notas->bind_param("i", $id_aluno);
    $stmt_notas->execute();
    $result_notas = $stmt_notas->get_result();
    $ultimas_notas = $result_notas->fetch_all(MYSQLI_ASSOC);
    $stmt_notas->close();

    // Buscar próximas atividades do filho
    $sql_atividades = "
        SELECT a.titulo, a.data_entrega, d.nome as nome_disciplina
        FROM atividades a
        JOIN professores_turmas_disciplinas ptd ON a.id_professor_turma_disciplina = ptd.id
        JOIN disciplinas d ON ptd.id_disciplina = d.id
        JOIN alunos_turmas at ON ptd.id_turma = at.id_turma
        WHERE at.id_aluno_usuario = ? AND a.data_entrega >= CURDATE()
        ORDER BY a.data_entrega ASC
        LIMIT 5
    ";
    $stmt_atividades = $conexao->prepare($sql_atividades);
    $stmt_atividades->bind_param("i", $id_aluno);
    $stmt_atividades->execute();
    $result_atividades = $stmt_atividades->get_result();
    $proximas_atividades = $result_atividades->fetch_all(MYSQLI_ASSOC);
    $stmt_atividades->close();

    $dados_filhos[$filho['nome']] = [
        'id' => $id_aluno,
        'ultimas_notas' => $ultimas_notas,
        'proximas_atividades' => $proximas_atividades
    ];
}

$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Responsável</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <div class="content-box">
            <h1>Painel do Responsável</h1>
            <p>Bem-vindo(a), <?php echo $nome_responsavel; ?>!</p>
            <hr>

            <?php if (empty($dados_filhos)): ?>
                <p style="text-align: center;">Não há alunos vinculados à sua conta. Por favor, entre em contacto com a secretaria da escola.</p>
            <?php else: ?>
                <?php foreach ($dados_filhos as $nome_filho => $dados): ?>
                    <details class="accordion-item" open>
                        <summary class="accordion-header"><?php echo htmlspecialchars($nome_filho); ?></summary>
                        <div class="accordion-content">
                            <div class="dashboard-grid">
                                <div class="dashboard-card">
                                    <h3>Próximas Atividades</h3>
                                    <?php if (!empty($dados['proximas_atividades'])): ?>
                                        <ul>
                                            <?php foreach ($dados['proximas_atividades'] as $atividade): ?>
                                                <li>
                                                    <span><strong><?php echo htmlspecialchars($atividade['titulo']); ?></strong><br><small><?php echo htmlspecialchars($atividade['nome_disciplina']); ?></small></span>
                                                    <span class="nota"><?php echo date('d/m/Y', strtotime($atividade['data_entrega'])); ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <a href="<?php echo BASE_URL; ?>/aluno/minhas_atividades.php?id_aluno=<?php echo $dados['id']; ?>" class="btn" style="display: block; text-align: center; margin-top: 20px;">Ver Todas</a>
                                    <?php else: ?>
                                        <p>Nenhuma atividade futura encontrada.</p>
                                    <?php endif; ?>
                                </div>

                                <div class="dashboard-card">
                                    <h3>Últimas Notas</h3>
                                    <?php if (!empty($dados['ultimas_notas'])): ?>
                                        <ul>
                                            <?php foreach ($dados['ultimas_notas'] as $nota): ?>
                                                <li>
                                                    <span><?php echo htmlspecialchars($nota['nome_disciplina']); ?></span>
                                                    <span class="nota"><?php echo htmlspecialchars(number_format($nota['valor_nota'], 2, ',', '.')); ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <a href="<?php echo BASE_URL; ?>/aluno/minhas_notas.php?id_aluno=<?php echo $dados['id']; ?>" class="btn" style="display: block; text-align: center; margin-top: 20px;">Ver Todas</a>
                                    <?php else: ?>
                                        <p>Nenhuma nota lançada ainda.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </details>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>