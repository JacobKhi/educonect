<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/conexao.php';

// Garante que apenas alunos acedam a esta página
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 1) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

$id_aluno = $_SESSION['usuario_id'];
$nome_usuario = htmlspecialchars($_SESSION['usuario_nome']);

// --- LÓGICA PARA BUSCAR OS DADOS DO PAINEL ---

// 1. Buscar últimas notas do aluno
$sql_notas = "
    SELECT n.valor_nota, d.nome AS nome_disciplina
    FROM notas n
    JOIN atividades a ON n.id_atividade = a.id
    JOIN professores_turmas_disciplinas ptd ON a.id_professor_turma_disciplina = ptd.id
    JOIN disciplinas d ON ptd.id_disciplina = d.id
    WHERE n.id_aluno_usuario = ?
    ORDER BY n.data_lancamento DESC
    LIMIT 3
";
$stmt_notas = $conexao->prepare($sql_notas);
$stmt_notas->bind_param("i", $id_aluno);
$stmt_notas->execute();
$result_notas = $stmt_notas->get_result();
$ultimas_notas = $result_notas->fetch_all(MYSQLI_ASSOC);
$stmt_notas->close();

// 2. Buscar próximas atividades do aluno
$sql_atividades = "
    SELECT a.titulo, a.data_entrega, d.nome as nome_disciplina
    FROM atividades a
    JOIN professores_turmas_disciplinas ptd ON a.id_professor_turma_disciplina = ptd.id
    JOIN disciplinas d ON ptd.id_disciplina = d.id
    JOIN alunos_turmas at ON ptd.id_turma = at.id_turma
    WHERE at.id_aluno_usuario = ? AND a.data_entrega >= CURDATE()
    ORDER BY a.data_entrega ASC
    LIMIT 3
";
$stmt_atividades = $conexao->prepare($sql_atividades);
$stmt_atividades->bind_param("i", $id_aluno);
$stmt_atividades->execute();
$result_atividades = $stmt_atividades->get_result();
$proximas_atividades = $result_atividades->fetch_all(MYSQLI_ASSOC);
$stmt_atividades->close();

// 3. Buscar as aulas de HOJE
$dia_semana_hoje = date('N'); // 'N' retorna 1 para Segunda, ..., 7 para Domingo
$sql_aulas = "
    SELECT 
        h.hora_inicio, 
        h.hora_fim, 
        d.nome AS nome_disciplina
    FROM horarios h
    JOIN professores_turmas_disciplinas ptd ON h.id_prof_turma_disc = ptd.id
    JOIN disciplinas d ON ptd.id_disciplina = d.id
    JOIN alunos_turmas at ON ptd.id_turma = at.id_turma
    WHERE at.id_aluno_usuario = ? AND h.dia_semana = ?
    ORDER BY h.hora_inicio ASC
";
$stmt_aulas = $conexao->prepare($sql_aulas);
$stmt_aulas->bind_param("ii", $id_aluno, $dia_semana_hoje);
$stmt_aulas->execute();
$result_aulas = $stmt_aulas->get_result();
$aulas_de_hoje = $result_aulas->fetch_all(MYSQLI_ASSOC);
$stmt_aulas->close();


$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Aluno</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <div class="content-box">
            <h1>Painel do Aluno</h1>
            <p>Bem-vindo(a), <?php echo $nome_usuario; ?>!</p>
            <hr>

            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>Aulas de Hoje</h3>
                    <?php if (!empty($aulas_de_hoje)): ?>
                        <ul>
                            <?php foreach ($aulas_de_hoje as $aula): ?>
                                <li>
                                    <span><strong><?php echo htmlspecialchars($aula['nome_disciplina']); ?></strong></span>
                                    <span class="nota"><?php echo date('H:i', strtotime($aula['hora_inicio'])); ?> - <?php echo date('H:i', strtotime($aula['hora_fim'])); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Nenhuma aula agendada para hoje.</p>
                    <?php endif; ?>
                </div>

                <div class="dashboard-card">
                    <h3>Próximas Atividades</h3>
                    <?php if (!empty($proximas_atividades)): ?>
                        <ul>
                            <?php foreach ($proximas_atividades as $atividade): ?>
                                <li>
                                    <span><strong><?php echo htmlspecialchars($atividade['titulo']); ?></strong><br><small><?php echo htmlspecialchars($atividade['nome_disciplina']); ?></small></span>
                                    <span class="nota"><?php echo date('d/m/Y', strtotime($atividade['data_entrega'])); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="<?php echo BASE_URL; ?>/aluno/minhas_atividades.php" class="btn" style="display: block; text-align: center; margin-top: 20px;">Ver Todas</a>
                    <?php else: ?>
                        <p>Nenhuma atividade futura encontrada.</p>
                    <?php endif; ?>
                </div>

                <div class="dashboard-card">
                    <h3>Últimas Notas</h3>
                    <?php if (!empty($ultimas_notas)): ?>
                        <ul>
                            <?php foreach ($ultimas_notas as $nota): ?>
                                <li>
                                    <span><?php echo htmlspecialchars($nota['nome_disciplina']); ?></span>
                                    <span class="nota"><?php echo htmlspecialchars(number_format($nota['valor_nota'], 2, ',', '.')); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="<?php echo BASE_URL; ?>/aluno/minhas_notas.php" class="btn" style="display: block; text-align: center; margin-top: 20px;">Ver Todas</a>
                    <?php else: ?>
                        <p>Nenhuma nota lançada ainda.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="menu-grid" style="grid-template-columns: 1fr; margin-top: 30px;">
                 <div class="menu-item">
                    <h3>Acesso Rápido</h3>
                    <a href="<?php echo BASE_URL; ?>/aluno/minha_frequencia.php">Minha Frequência</a>
                    <a href="<?php echo BASE_URL; ?>/aluno/meus_materiais.php">Meus Materiais</a>
                </div>
            </div>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>