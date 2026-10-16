<?php
define('NA_RAIZ', true);
require_once __DIR__ . '/php/includes/bootstrap.php';

// Sair
if (isset($_GET['logout'])) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    header('Location: php/login.php');
    exit;
}

// Visitante sem login vai para a tela de login (a não ser que LOJA_PUBLICA seja true).
exigir_acesso();

$categorias = categorias();
$destaques  = produtos_recentes(10);

$titulo = 'Início';
require __DIR__ . '/php/includes/header.php';
?>

<main>
    <section class="hero">
        <div class="hero-texto">
            <h1>Tudo pro seu lado geek</h1>
            <p>Action figures, Funko Pop, camisetas, acessórios e muito Pokémon. Escolha uma categoria e monte sua coleção.</p>
            <a class="btn btn-cheio" href="<?= e(pg('produtos.php')) ?>">Ver produtos</a>
            <a class="btn" href="#categorias">Ver categorias</a>
        </div>
        <img class="hero-logo" src="<?= e(asset('img/logo.png')) ?>" alt="Los Pollos Geek">
    </section>

    <section class="secao" id="categorias">
        <div class="secao-topo">
            <h2>Categorias</h2>
        </div>
        <div class="grade-categorias">
            <?php foreach ($categorias as $c): $img = imagem_categoria($c); ?>
                <a class="cat-card" href="<?= e(pg('categoria.php')) ?>?c=<?= e($c['slug']) ?>">
                    <?php if ($img): ?><img src="<?= e($img) ?>" alt="" loading="lazy"><?php endif; ?>
                    <strong><?= e($c['nome']) ?></strong>
                    <small><?php $n = contar_produtos($c['id']); echo $n . ($n === 1 ? ' produto' : ' produtos'); ?></small>
                </a>
            <?php endforeach; ?>
        </div>
        <?php if (admin()): ?>
            <p class="acao-admin"><a href="<?= e(pg('nova_categoria.php')) ?>">+ Nova categoria</a></p>
        <?php endif; ?>
    </section>

    <section class="secao secao-cinza">
        <div class="secao-topo">
            <h2>Últimos produtos</h2>
            <a class="ver-todos" href="<?= e(pg('produtos.php')) ?>">Ver todos</a>
        </div>

        <?php if ($destaques): ?>
            <div class="grade-produtos">
                <?php foreach ($destaques as $p) echo card_produto($p); ?>
            </div>
        <?php else: ?>
            <div class="vazio">
                <h3>Nenhum produto cadastrado ainda</h3>
                <p>Os produtos aparecem aqui assim que forem adicionados.</p>
                <?php if (admin()): ?><a class="btn btn-cheio" href="<?= e(pg('criar_produto.php')) ?>">+ Novo produto</a><?php endif; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="chamada">
        <div>
            <h2>Não achou o que procurava?</h2>
            <p>Fale com a gente pelo WhatsApp ou por e-mail.</p>
        </div>
        <div class="chamada-botoes">
            <a class="btn btn-azul" href="https://wa.me/<?= e(preg_replace('/\D/', '', LOJA_TELEFONE)) ?>">WhatsApp</a>
            <a class="btn" href="mailto:<?= e(LOJA_EMAIL) ?>">E-mail</a>
        </div>
    </section>
</main>

<?php require __DIR__ . '/php/includes/footer.php';
