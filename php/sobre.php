<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_acesso();

$titulo = 'Sobre nós';
require __DIR__ . '/includes/header.php';
?>

<main class="pagina pagina-estreita">
    <h1>Sobre a <?= e(LOJA_NOME) ?></h1>

    <p>
        A <?= e(LOJA_NOME) ?> é uma loja de itens da cultura geek: action figures, Funko Pop, camisetas,
        acessórios e produtos de Pokémon. Nosso lema é <em>"<?= e(LOJA_SLOGAN) ?>"</em>:
        quem é geek reconhece de primeira.
    </p>

    <h2>O que você encontra aqui</h2>
    <ul class="sobre-lista">
        <?php foreach (categorias() as $c): ?>
            <li>
                <a href="categoria.php?c=<?= e($c['slug']) ?>"><strong><?= e($c['nome']) ?></strong></a>
                <?php if ($c['descricao'] !== ''): ?> — <?= e($c['descricao']) ?><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>Como comprar</h2>
    <ol class="sobre-lista">
        <li>Crie sua conta ou entre com o seu e-mail.</li>
        <li>Navegue pelas categorias e adicione os produtos ao carrinho.</li>
        <li>Revise o carrinho e finalize a compra.</li>
    </ol>

    <h2>Fale com a gente</h2>
    <p>
        WhatsApp: <a href="https://wa.me/<?= e(preg_replace('/\D/', '', LOJA_TELEFONE)) ?>"><?= e(LOJA_TELEFONE) ?></a><br>
        E-mail: <a href="mailto:<?= e(LOJA_EMAIL) ?>"><?= e(LOJA_EMAIL) ?></a>
    </p>

    <p><a class="btn btn-cheio" href="produtos.php">Ver produtos</a></p>
</main>

<?php require __DIR__ . '/includes/footer.php';
