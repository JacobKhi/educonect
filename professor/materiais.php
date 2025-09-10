<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once BASE_PATH . '/conexao.php';

// Bloco de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 2) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

$id_professor = $_SESSION['usuario_id'];
$mensagem_sucesso = '';
$mensagem_erro = '';

// Lógica para processar o upload do material
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['arquivo_material'])) {
    $id_prof_turma_disc = $_POST['id_prof_turma_disc'];
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    
    $nome_arquivo = basename($_FILES["arquivo_material"]["name"]);
    $caminho_destino = BASE_PATH . '/uploads/' . $nome_arquivo;
    $caminho_bd = '/uploads/' . $nome_arquivo; // Caminho a ser guardado na BD

    // Tenta mover o ficheiro para a pasta de uploads
    if (move_uploaded_file($_FILES["arquivo_material"]["tmp_name"], $caminho_destino)) {
        $sql = "INSERT INTO materiais_estudo (id_professor_turma_disciplina, titulo, descricao, caminho_arquivo) VALUES (?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("isss", $id_prof_turma_disc, $titulo, $descricao, $caminho_bd);
        
        if ($stmt->execute()) {
            $mensagem_sucesso = "Material enviado com sucesso!";
        } else {
            $mensagem_erro = "Erro ao guardar as informações na base de dados.";
        }
        $stmt->close();
    } else {
        $mensagem_erro = "Ocorreu um erro ao fazer o upload do ficheiro.";
    }
}

// Lógica para excluir um material
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id_material = $_GET['id'];
    // Primeiro, busca o caminho do ficheiro para o apagar do servidor
    $sql_select = "SELECT caminho_arquivo FROM materiais_estudo WHERE id = ?";
    $stmt_select = $conexao->prepare($sql_select);
    $stmt_select->bind_param("i", $id_material);
    $stmt_select->execute();
    $resultado = $stmt_select->get_result()->fetch_assoc();
    if ($resultado && file_exists(BASE_PATH . $resultado['caminho_arquivo'])) {
        unlink(BASE_PATH . $resultado['caminho_arquivo']); // Apaga o ficheiro físico
    }
    $stmt_select->close();

    // Depois, apaga o registo da base de dados
    $sql_delete = "DELETE FROM materiais_estudo WHERE id = ?";
    $stmt_delete = $conexao->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_material);
    $stmt_delete->execute();
    $stmt_delete->close();
    header("Location: materiais.php");
    exit();
}


// Busca as turmas e disciplinas do professor
$sql_alocacoes = "SELECT ptd.id, t.nome AS nome_turma, d.nome AS nome_disciplina FROM professores_turmas_disciplinas ptd JOIN turmas t ON ptd.id_turma = t.id JOIN disciplinas d ON ptd.id_disciplina = d.id WHERE ptd.id_professor_usuario = ? ORDER BY t.nome, d.nome";
$stmt_alocacoes = $conexao->prepare($sql_alocacoes);
$stmt_alocacoes->bind_param("i", $id_professor);
$stmt_alocacoes->execute();
$result_alocacoes = $stmt_alocacoes->get_result();

// Busca os materiais já enviados pelo professor
$sql_materiais = "SELECT m.id, m.titulo, m.descricao, t.nome AS nome_turma, d.nome AS nome_disciplina FROM materiais_estudo m JOIN professores_turmas_disciplinas ptd ON m.id_professor_turma_disciplina = ptd.id JOIN turmas t ON ptd.id_turma = t.id JOIN disciplinas d ON ptd.id_disciplina = d.id WHERE ptd.id_professor_usuario = ? ORDER BY m.data_criacao DESC";
$stmt_materiais = $conexao->prepare($sql_materiais);
$stmt_materiais->bind_param("i", $id_professor);
$stmt_materiais->execute();
$result_materiais = $stmt_materiais->get_result();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Materiais de Estudo</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>
    <div class="container">
        <div class="content-box">
            <h1>Gerenciar Materiais de Estudo</h1>
            <?php if($mensagem_sucesso): ?><div class="msg-sucesso" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;"><?php echo $mensagem_sucesso; ?></div><?php endif; ?>
            <?php if($mensagem_erro): ?><div class="erro"><?php echo $mensagem_erro; ?></div><?php endif; ?>

            <div class="form-and-list-layout">
                <div class="form-section">
                    <h3>Adicionar Novo Material</h3>
                    <form action="materiais.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="id_prof_turma_disc">Para a Turma/Disciplina:</label>
                            <select id="id_prof_turma_disc" name="id_prof_turma_disc" required>
                                <option value="">-- Selecione --</option>
                                <?php mysqli_data_seek($result_alocacoes, 0); while($aloc = $result_alocacoes->fetch_assoc()): ?>
                                    <option value="<?php echo $aloc['id']; ?>"><?php echo htmlspecialchars($aloc['nome_turma'] . " - " . $aloc['nome_disciplina']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="titulo">Título do Material:</label>
                            <input type="text" id="titulo" name="titulo" required>
                        </div>
                        <div class="form-group">
                            <label for="descricao">Descrição (opcional):</label>
                            <textarea id="descricao" name="descricao" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="arquivo_material">Ficheiro:</label>
                            <input type="file" id="arquivo_material" name="arquivo_material" required>
                        </div>
                        <button type="submit" class="btn">Enviar Material</button>
                    </form>
                </div>
                <div class="list-section">
                    <h3>Materiais Enviados</h3>
                    <?php if ($result_materiais && $result_materiais->num_rows > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Turma/Disciplina</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($mat = $result_materiais->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($mat['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($mat['nome_turma'] . " - " . $mat['nome_disciplina']); ?></td>
                                        <td class="actions-cell">
                                            <a href="editar_material.php?id=<?php echo $mat['id']; ?>" class="btn btn-small btn-edit">Editar</a>
                                            <a href="materiais.php?acao=excluir&id=<?php echo $mat['id']; ?>" class="btn btn-small btn-delete" onclick="return confirm('Tem a certeza que deseja excluir este material?')">Excluir</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Nenhum material enviado ainda.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>