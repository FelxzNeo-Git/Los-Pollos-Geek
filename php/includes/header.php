<?php
/**
 * Cabeçalho comum a todas as páginas da loja.
 * Variáveis esperadas: $titulo (string) e, opcionalmente, $css (array de css extras)
 * e $categoriaAtual (categoria aberta, para destacar no menu).
 */
$css            = $css ?? [];
$categoriaAtual = $categoriaAtual ?? null;
$qtdCarrinho    = carrinho_qtd();
$aviso          = flash('aviso');
$paginaAtual    = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> | <?= e(LOJA_NOME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= e(asset('img/favicon.png')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/estilo.css')) ?>">
    <?php foreach ($css as $arquivo): ?>
    <link rel="stylesheet" href="<?= e(asset('css/' . $arquivo)) ?>">
    <?php endforeach; ?>
</head>
<body>

<header class="topo">
    <a class="logo" href="<?= e(asset('index.php')) ?>">
        <img src="<?= e(asset('img/logo.png')) ?>" alt="">
        <span><?= e(LOJA_NOME) ?></span>
    </a>

    <div class="topo-conta">
        <?php if (logado()): ?>
            <div class="menu-usuario">
                <button class="user-button" type="button" onclick="toggleNav()">
                    Olá, <?= e(explode(' ', trim($_SESSION['usuario_nome'] ?? ''))[0]) ?>
                </button>
                <nav class="navbar" aria-label="Conta">
                    <a href="<?= e(pg('perfil.php')) ?>">Perfil</a>
                    <a href="<?= e(pg('carrinho.php')) ?>">Compras</a>
                    <a href="<?= e(pg('sobre.php')) ?>">Sobre</a>
                    <?php if (admin()): ?><a href="<?= e(pg('admin.php')) ?>">Painel</a><?php endif; ?>
                    <a href="<?= e(asset('index.php')) ?>?logout">Sair</a>
                </nav>
            </div>
        <?php else: ?>
            <a href="<?= e(pg('login.php')) ?>">Entrar</a>
            <a href="<?= e(pg('cadastro.php')) ?>">Criar conta</a>
        <?php endif; ?>

        <a class="carrinho" href="<?= e(pg('carrinho.php')) ?>">Carrinho (<?= $qtdCarrinho ?>)</a>
    </div>
</header>

<nav class="menu-categorias" aria-label="Categorias">
    <a href="<?= e(pg('produtos.php')) ?>" class="<?= $paginaAtual === 'produtos.php' ? 'ativo' : '' ?>">Todos os produtos</a>
    <?php foreach (categorias() as $c): ?>
        <a href="<?= e(pg('categoria.php')) ?>?c=<?= e($c['slug']) ?>"
           class="<?= $categoriaAtual && $categoriaAtual['id'] === $c['id'] && $paginaAtual === 'categoria.php' ? 'ativo' : '' ?>"><?= e($c['nome']) ?></a>
    <?php endforeach; ?>
</nav>

<?php if ($aviso): ?>
    <p class="aviso"><?= e($aviso) ?></p>
<?php endif; ?>
