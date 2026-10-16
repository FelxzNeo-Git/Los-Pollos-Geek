<?php
/**
 * Lista de produtos: todos (produtos.php) ou de uma categoria (categoria.php).
 * Variável esperada: $categoriaAtual (array da categoria ou null).
 */
exigir_acesso();

$categoriaAtual = $categoriaAtual ?? null;
$produtos = produtos_recentes(null, $categoriaAtual['id'] ?? null);
$total    = count($produtos);
$novoLink = pg('criar_produto.php') . ($categoriaAtual ? '?c=' . rawurlencode($categoriaAtual['slug']) : '');

$titulo = $categoriaAtual['nome'] ?? 'Todos os produtos';
require __DIR__ . '/header.php';
?>

<main class="pagina">
    <h1><?= e($titulo) ?></h1>
    <?php if (!empty($categoriaAtual['descricao'])): ?>
        <p class="texto-suave"><?= e($categoriaAtual['descricao']) ?></p>
    <?php endif; ?>

    <?php if ($produtos): ?>
        <p class="texto-suave"><?= $total ?> <?= $total === 1 ? 'produto' : 'produtos' ?></p>
        <div class="grade-produtos">
            <?php foreach ($produtos as $p) echo card_produto($p); ?>
        </div>
        <?php if (admin()): ?>
            <p class="acao-admin"><a href="<?= e($novoLink) ?>">+ Novo produto</a></p>
        <?php endif; ?>
    <?php else: ?>
        <div class="vazio">
            <h3>Nenhum produto por aqui ainda</h3>
            <p>Os produtos aparecem aqui assim que forem adicionados.</p>
            <?php if (admin()): ?><a class="btn btn-cheio" href="<?= e($novoLink) ?>">+ Novo produto</a><?php endif; ?>
        </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/footer.php';
