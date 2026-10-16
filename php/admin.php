<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_admin();

$titulo = 'Painel';
$css    = ['admin.css'];
require __DIR__ . '/includes/header.php';
?>
<main class="admin-main admin-largo">
    <h1>Painel do admin</h1>
    <p class="admin-acoes">
        <a class="btn btn-cheio" href="nova_categoria.php">+ Nova categoria</a>
        <a class="btn btn-cheio" href="criar_produto.php">+ Novo produto</a>
    </p>

    <?php if (!categorias()): ?>
        <p class="vazio-min">Nenhuma categoria. <a href="nova_categoria.php">Criar a primeira</a></p>
    <?php endif; ?>

    <?php foreach (categorias() as $c): ?>
        <section class="admin-bloco">
            <div class="admin-linha admin-cat">
                <a href="categoria.php?c=<?= e($c['slug']) ?>"><strong><?= e($c['nome']) ?></strong></a>
                <span class="adm-acoes">
                    <a href="criar_produto.php?c=<?= e($c['slug']) ?>">+ produto</a>
                    <form method="post" action="excluir.php" onsubmit="return confirm('Excluir a categoria e todos os seus produtos?')">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="tipo" value="categoria">
                        <input type="hidden" name="id" value="<?= e($c['id']) ?>">
                        <button class="del" type="submit">excluir</button>
                    </form>
                </span>
            </div>

            <?php $produtos = produtos_da_categoria($c['id']); ?>
            <?php if (!$produtos): ?>
                <p class="vazio-min">Nenhum produto nesta categoria.</p>
            <?php endif; ?>

            <?php foreach ($produtos as $p): ?>
                <div class="admin-linha admin-prod">
                    <span><a href="produto.php?id=<?= e($p['id']) ?>"><?= e($p['nome']) ?></a> · <?= preco($p['valor']) ?></span>
                    <form method="post" action="excluir.php" onsubmit="return confirm('Excluir este produto?')">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="tipo" value="produto">
                        <input type="hidden" name="id" value="<?= e($p['id']) ?>">
                        <button class="del" type="submit">excluir</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endforeach; ?>
</main>

<?php require __DIR__ . '/includes/footer.php';
