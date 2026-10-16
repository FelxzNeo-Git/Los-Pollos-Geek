<?php
require_once __DIR__ . '/config.php';

// Quem já está logado vai direto para a loja
if (!empty($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | <?= htmlspecialchars(LOJA_NOME) ?></title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body class="auth-login">

<main class="auth">
    <div class="auth-arte" aria-hidden="true"></div>

    <section class="auth-form">
        <img class="auth-logo" src="../img/logo.png" alt="<?= htmlspecialchars(LOJA_NOME) ?>">

        <h1>Entrar</h1>
        <p class="auth-sub">Acesse sua conta para ver os produtos e comprar.</p>

        <?php if (!empty($_SESSION['erro'])): ?>
            <p class="msg msg-erro"><?= htmlspecialchars($_SESSION['erro']) ?></p>
            <?php unset($_SESSION['erro']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['sucesso'])): ?>
            <p class="msg msg-sucesso"><?= htmlspecialchars($_SESSION['sucesso']) ?></p>
            <?php unset($_SESSION['sucesso']); ?>
        <?php endif; ?>

        <form method="post" action="processar_login.php">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required autocomplete="email">

            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required autocomplete="current-password">

            <button type="submit" class="btn-acessar">Entrar</button>
        </form>

        <p class="auth-troca">Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
    </section>
</main>

</body>
</html>
