<?php
session_start();
require_once __DIR__ . '/config.php';
require_once BASE_PATH . '/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

$id_usuario_logado = $_SESSION['usuario_id'];
$nome_usuario = htmlspecialchars($_SESSION['usuario_nome']);
$perfil_usuario = $_SESSION['usuario_perfil_id'];

$ultimas_notas = [];
if ($perfil_usuario == 1) {
    $sql_notas = "SELECT n.valor_nota, d.nome AS nome_disciplina FROM notas n JOIN atividades a ON n.id_atividade = a.id JOIN professores_turmas_disciplinas ptd ON a.id_prof_turma_disc = ptd.id JOIN disciplinas d ON ptd.id_disciplina = d.id WHERE n.id_aluno_usuario = ? ORDER BY n.data_lancamento DESC LIMIT 5";
    $stmt = $conexao->prepare($sql_notas);
    $stmt->bind_param("i", $id_usuario_logado);
    $stmt->execute();
    $resultado_notas = $stmt->get_result();
    while ($nota = $resultado_notas->fetch_assoc()) {
        $ultimas_notas[] = $nota;
    }
    $stmt->close();
}
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Principal</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
    <style>
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .dashboard-card { background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .dashboard-card h3 { margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .dashboard-card ul { list-style: none; padding: 0; }
        .dashboard-card li { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
        .nota { font-weight: bold; }
    </style>
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <h1>Painel Principal</h1>
        <p>Bem-vindo(a), <?php echo $nome_usuario; ?>!</p>
        <hr style="margin-bottom: 30px;">
        
        <?php if ($perfil_usuario == 1): // Se for Aluno ?>
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>Últimas Notas</h3>
                    <?php if (!empty($ultimas_notas)): ?>
                        <ul>
                            <?php foreach ($ultimas_notas as $nota): ?>
                                <li><span><?php echo htmlspecialchars($nota['nome_disciplina']); ?></span><span class="nota"><?php echo htmlspecialchars($nota['valor_nota']); ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Nenhuma nota lançada ainda.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="content-box">
                <p>Este é o seu painel principal.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>