<?php 
// O require da navbar continua aqui para que utilizadores logados vejam a versão correta dela
require_once 'includes/navbar.php'; 
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Bem-vindo ao EduConect</title>
    <link rel="stylesheet" href="/educonect/css/global.css">
    <link rel="stylesheet" href="/educonect/css/navbar.css">
    <link rel="stylesheet" href="/educonect/css/footer.css">
    <link rel="stylesheet" href="/educonect/css/home.css">
</head>
<body>
    <main>
        <section class="hero-section">
            <h1>EduConect</h1>
            <p>A ponte entre alunos, professores e responsáveis. Todas as informações académicas num só lugar, de forma simples e organizada.</p>
            <a href="<?php echo BASE_URL; ?>/auth/login/login.php" class="cta-button">Aceder ao Portal</a>
        </section>

        <section class="features-section">
            <h2>Um Portal para Todos</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <h3>Para Alunos</h3>
                    <p>Consulte as suas notas, acompanhe as atividades, veja a sua frequência e tenha controlo total sobre a sua vida académica.</p>
                </div>
                <div class="feature-card">
                    <h3>Para Professores</h3>
                    <p>Lance notas e atividades de forma rápida, registe a frequência e comunique com as suas turmas de maneira eficiente.</p>
                </div>
                <div class="feature-card">
                    <h3>Para Responsáveis</h3>
                    <p>Acompanhe de perto o desempenho escolar dos seus filhos, receba comunicados e participe ativamente da sua educação.</p>
                </div>
            </div>
        </section>

        <section class="registration-section">
            <h2>É o seu primeiro acesso?</h2>
            <p>Se você recebeu um código de convite da sua escola, comece por aqui.</p>
            <div class="registration-buttons">
                <a href="<?php echo BASE_URL; ?>/auth/cadastro_aluno/finalizar_cadastro_aluno.php" class="btn-registration">Sou Aluno</a>
                <a href="<?php echo BASE_URL; ?>/auth/cadastro_professor/finalizar_cadastro_professor.php" class="btn-registration">Sou Professor</a>
                <a href="<?php echo BASE_URL; ?>/auth/cadastro_responsavel/finalizar_cadastro_responsavel.php" class="btn-registration">Sou Responsável</a>
            </div>
        </section>
    </main>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>