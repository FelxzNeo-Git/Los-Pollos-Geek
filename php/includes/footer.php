<footer class="rodape">
    <div class="rodape-grade">
        <div>
            <p class="rodape-marca"><?= e(LOJA_NOME) ?></p>
            <p><?= e(LOJA_SLOGAN) ?></p>
        </div>
        <div>
            <h4>Categorias</h4>
            <?php foreach (categorias() as $c): ?>
                <a href="<?= e(pg('categoria.php')) ?>?c=<?= e($c['slug']) ?>"><?= e($c['nome']) ?></a>
            <?php endforeach; ?>
        </div>
        <div>
            <h4>Loja</h4>
            <a href="<?= e(pg('produtos.php')) ?>">Todos os produtos</a>
            <a href="<?= e(pg('carrinho.php')) ?>">Carrinho</a>
            <a href="<?= e(pg('perfil.php')) ?>">Minha conta</a>
            <a href="<?= e(pg('sobre.php')) ?>">Sobre nós</a>
        </div>
        <div>
            <h4>Contato</h4>
            <a href="https://wa.me/<?= e(preg_replace('/\D/', '', LOJA_TELEFONE)) ?>"><?= e(LOJA_TELEFONE) ?></a>
            <a href="mailto:<?= e(LOJA_EMAIL) ?>"><?= e(LOJA_EMAIL) ?></a>
        </div>
    </div>
    <p class="rodape-base">© <?= date('Y') ?> <?= e(LOJA_NOME) ?>. Todos os direitos reservados.</p>
</footer>
<script src="<?= e(asset('js/loja.js')) ?>"></script>
</body>
</html>
