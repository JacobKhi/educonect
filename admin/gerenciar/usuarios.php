<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once BASE_PATH . '/conexao.php';

// Bloco de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil_id'] != 4) {
    header("Location: " . BASE_URL . "/auth/login/index.php");
    exit();
}

// Busca todos os usuários e os agrupa por perfil
$sql = "SELECT u.nome, u.email, da.matricula, p.tipo 
        FROM usuarios u
        LEFT JOIN dados_alunos da ON u.id = da.id_usuario
        JOIN perfis p ON u.id_perfil = p.id
        ORDER BY p.id, u.nome ASC";
$result = $conexao->query($sql);

$usuarios_por_perfil = [];
if($result) {
    while($usuario = $result->fetch_assoc()) {
        $usuarios_por_perfil[$usuario['tipo']][] = $usuario;
    }
}
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Usuários Ativos</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/layouts.css">
</head>
<body>
    <?php require_once BASE_PATH . '/includes/navbar.php'; ?>

    <div class="container">
        <div class="content-box">
            <h1>Usuários Ativos no Sistema</h1>

            <?php if (!empty($usuarios_por_perfil)): ?>
                <?php foreach ($usuarios_por_perfil as $perfil => $usuarios): ?>
                    <details class="accordion-item" open>
                        <summary class="accordion-header"><?php echo ucfirst($perfil) . 's'; ?> (<?php echo count($usuarios); ?>)</summary>
                        <div class="accordion-content">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Nome Completo</th>
                                        <?php if ($perfil == 'aluno'): ?>
                                            <th>Matrícula</th>
                                        <?php else: ?>
                                            <th>Email</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                            <?php if ($perfil == 'aluno'): ?>
                                                <td><?php echo htmlspecialchars($usuario['matricula']); ?></td>
                                            <?php else: ?>
                                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </details>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhum usuário ativo encontrado.</p>
            <?php endif; ?>

        </div>
    </div>
    <?php require_once BASE_PATH . '/includes/footer.php'; ?>
</body>
</html>