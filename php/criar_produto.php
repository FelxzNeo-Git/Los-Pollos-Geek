<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_admin();

$erros  = flash('erros') ?? [];
$antigo = flash('antigo') ?? [];
$todas  = categorias();

// Pré-seleciona a categoria vinda do link (?c=slug) ou do formulário anterior
$catSel = $antigo['categoria'] ?? '';
if ($catSel === '' && isset($_GET['c'])) {
    $c = categoria_por_slug((string) $_GET['c']);
    $catSel = $c['id'] ?? '';
}

$titulo = 'Novo produto';
$css    = ['admin.css'];
require __DIR__ . '/includes/header.php';
?>
<main class="admin-main">
    <div class="box">
        <h1>Novo produto</h1>

        <?php if ($erros): ?>
            <div class="erros"><?php foreach ($erros as $erro): ?><p><?= e($erro) ?></p><?php endforeach; ?></div>
        <?php endif; ?>

        <?php if (!$todas): ?>
            <div class="erros"><p>Crie uma categoria antes de cadastrar produtos.</p></div>
            <a class="link" href="nova_categoria.php">+ Nova categoria</a>
        <?php else: ?>
        <form action="processar_produto.php" method="post" enctype="multipart/form-data" novalidate id="form">
            <?= csrf_campo() ?>

            <label for="categoria">Categoria</label>
            <select id="categoria" name="categoria" required>
                <option value="">Escolha a categoria</option>
                <?php foreach ($todas as $c): ?>
                    <option value="<?= e($c['id']) ?>" <?= $catSel === $c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="nome">Nome do produto</label>
            <input type="text" id="nome" name="nome" required minlength="3" maxlength="80" value="<?= e($antigo['nome'] ?? '') ?>" placeholder="Ex.: Funko Pop Naruto Uzumaki">

            <label for="descricao">Descrição <small>(opcional)</small></label>
            <textarea id="descricao" name="descricao" rows="4" maxlength="500" placeholder="Material, tamanho, edição..."><?= e($antigo['descricao'] ?? '') ?></textarea>

            <label for="valor">Valor (R$)</label>
            <input type="text" id="valor" name="valor" required inputmode="decimal" value="<?= e($antigo['valor'] ?? '') ?>" placeholder="129,90">

            <label for="valor_antigo">Valor original <small>(opcional, mostra o selo de desconto)</small></label>
            <input type="text" id="valor_antigo" name="valor_antigo" inputmode="decimal" value="<?= e($antigo['valor_antigo'] ?? '') ?>" placeholder="159,90">

            <label for="imagem">Imagem do produto</label>
            <label class="upload" for="imagem">
                <span id="upload-texto">Clique para escolher uma imagem (JPG, PNG, WEBP ou GIF, até 5 MB)</span>
                <input type="file" id="imagem" name="imagem" accept="image/jpeg,image/png,image/webp,image/gif" required>
                <img id="preview" alt="Pré-visualização">
            </label>

            <button class="btn btn-cheio" type="submit">Adicionar produto</button>
        </form>
        <a class="link" href="admin.php">Voltar ao painel</a>
        <?php endif; ?>
    </div>
</main>
<script src="../js/admin.js"></script>

<?php require __DIR__ . '/includes/footer.php';
