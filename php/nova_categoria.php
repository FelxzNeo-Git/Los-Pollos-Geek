<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_admin();

$erros  = flash('erros') ?? [];
$antigo = flash('antigo') ?? [];

$titulo = 'Nova categoria';
$css    = ['admin.css'];
require __DIR__ . '/includes/header.php';
?>
<main class="admin-main">
    <div class="box">
        <h1>Nova categoria</h1>
        <p class="texto-suave">Cada categoria ganha uma página própria na loja (ex.: Funko Pop, Camisetas, Pokémon).</p>

        <?php if ($erros): ?>
            <div class="erros"><?php foreach ($erros as $erro): ?><p><?= e($erro) ?></p><?php endforeach; ?></div>
        <?php endif; ?>

        <form action="processar_categoria.php" method="post" enctype="multipart/form-data" novalidate>
            <?= csrf_campo() ?>

            <label for="nome">Nome da categoria</label>
            <input type="text" id="nome" name="nome" required minlength="2" maxlength="60" value="<?= e($antigo['nome'] ?? '') ?>" placeholder="Ex.: Mangás">

            <label for="descricao">Descrição <small>(opcional)</small></label>
            <textarea id="descricao" name="descricao" rows="3" maxlength="200" placeholder="Uma frase sobre os produtos desta categoria"><?= e($antigo['descricao'] ?? '') ?></textarea>

            <label for="imagem">Imagem <small>(opcional)</small></label>
            <label class="upload" for="imagem">
                <span id="upload-texto">Clique para escolher uma imagem (JPG, PNG, WEBP ou GIF, até 5 MB)</span>
                <input type="file" id="imagem" name="imagem" accept="image/jpeg,image/png,image/webp,image/gif">
                <img id="preview" alt="Pré-visualização">
            </label>

            <button class="btn btn-cheio" type="submit">Criar categoria</button>
        </form>
        <a class="link" href="admin.php">Voltar ao painel</a>
    </div>
</main>
<script src="../js/admin.js"></script>

<?php require __DIR__ . '/includes/footer.php';
