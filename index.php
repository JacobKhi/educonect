<?php 
// O require_once para a navbar precisa do caminho para a pasta includes
require_once 'includes/navbar.php'; 
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Página Inicial - EduConect</title>
    <link rel="stylesheet" href="/educonect/css/global.css">
    <link rel="stylesheet" href="/educonect/css/navbar.css">
    <link rel="stylesheet" href="/educonect/css/footer.css">
</head>
<body>
    <div style="text-align: center; padding-top: 50px;">
        <h1>Bem-vindo ao Sistema EduConect</h1>
        <p>Use a barra de navegação acima para acessar as áreas do sistema.</p>
    </div>
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>