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

// Buscar todos os materiais de estudo para o aluno
$sql_materiais = "
    SELECT 
        d.nome AS nome_disciplina,
        m.titulo,
        m.descricao,
        m.caminho_arquivo
    FROM materiais_estudo m
    JOIN professores_turmas_disciplinas ptd ON m.id_professor_turma_disciplina = ptd.id
    JOIN disciplinas d ON ptd.id_disciplina = d.id
    JOIN alunos_turmas at ON ptd.id_turma = at.id_turma
    WHERE at.id_aluno_usuario = ?
    ORDER BY d.nome ASC, m.data_criacao DESC
";
$stmt = $conexao->prepare($sql_materiais);
$stmt->bind_param("i", $id_aluno);
$stmt->execute();
$result_materiais = $stmt->get_result();

$materiais_por_disciplina = [];
while ($row = $result_materiais->fetch_assoc()) {
    $materiais_por_disciplina[$row['nome_disciplina']][] = $row;
}
$stmt->close();
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Materiais de Estudo</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <div class="content-box">
            <h1>Meus Materiais de Estudo</h1>
            <p>Aceda aqui a todos os ficheiros e links partilhados pelos seus professores.</p>
            <hr>

            <?php if (empty($materiais_por_disciplina)): ?>
                <p style="text-align: center; margin-top: 20px;">Nenhum material de estudo foi partilhado consigo ainda.</p>
            <?php else: ?>
                <?php foreach ($materiais_por_disciplina as $disciplina => $materiais): ?>
                    <details class="accordion-item" open>
                        <summary class="accordion-header"><?php echo htmlspecialchars($disciplina); ?></summary>
                        <div class="accordion-content">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Descrição</th>
                                        <th style="width: 120px;">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($materiais as $material): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($material['titulo']); ?></td>
                                            <td><?php echo htmlspecialchars($material['descricao']); ?></td>
                                            <td>
                                                <a href="<?php echo BASE_URL . htmlspecialchars($material['caminho_arquivo']); ?>" class="btn btn-small" download>Descarregar</a>
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