<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

$cat = catalogo();

$produtos = [];
foreach ($cat['produtos'] ?? [] as $p) {
    $produtos[(string) $p['id']] = $p;
}
function destino_seguro(string $url, string $padrao): string
{
    return preg_match('~^(\.\./)?[a-z_]+\.php(\?[A-Za-z0-9_=&%.+\-]*)?$~', $url) ? $url : $padrao;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $acao   = $_POST['acao'] ?? '';
    $voltar = destino_seguro((string) ($_POST['voltar'] ?? ''), 'carrinho.php');


    if ($acao === 'pagar') {
        $itens = $_SESSION['carrinho'] ?? [];
        $totalPedido = 0;

        foreach ($itens as $idItem => $qtd) {
            if (isset($produtos[(string) $idItem])) {
                $totalPedido += (float) $produtos[(string) $idItem]['valor'] * (int) $qtd;
            }
        }

        if ($totalPedido > 0) {
            $_SESSION['pedido_ok'] = $totalPedido;
            $_SESSION['carrinho'] = [];
        }

        ir('carrinho.php');
    }

    $id = (string) ($_POST['id'] ?? '');

    if (isset($produtos[$id])) {
        $atual = (int) ($_SESSION['carrinho'][$id] ?? 0);

        switch ($acao) {
            case 'adicionar':
                $qtd = min(20, max(1, (int) ($_POST['qtd'] ?? 1)));
                $_SESSION['carrinho'][$id] = $atual + $qtd;
                flash('aviso', $produtos[$id]['nome'] . ' foi adicionado ao carrinho.');
                break;

            case 'mais':
                $_SESSION['carrinho'][$id] = $atual + 1;
                if ($voltar !== 'carrinho.php') {
                    flash('aviso', $produtos[$id]['nome'] . ' foi adicionado ao carrinho.');
                }
                break;

            case 'menos':
                if ($atual > 1) {
                    $_SESSION['carrinho'][$id] = $atual - 1;
                } else {
                    unset($_SESSION['carrinho'][$id]);
                }
                break;

            case 'remover':
                unset($_SESSION['carrinho'][$id]);
                break;
        }
    }

    ir($voltar);
}


$pedidoOk = $_SESSION['pedido_ok'] ?? null;
unset($_SESSION['pedido_ok']);

$carrinho = $_SESSION['carrinho'] ?? [];
$total = 0;

$titulo = 'Meu carrinho';
require __DIR__ . '/includes/header.php';
?>

<main class="pagina pagina-estreita">
    <h1>Meu carrinho</h1>

    <?php if ($pedidoOk !== null): ?>
        <div class="pedido-ok">
            <h3>Pedido realizado com sucesso!</h3>
            <p>Total pago: <strong><?= preco($pedidoOk) ?></strong></p>
        </div>
    <?php endif; ?>

    <?php if (empty($carrinho)): ?>

        <div class="vazio">
            <h3>Seu carrinho está vazio</h3>
            <p>Escolha seus produtos e eles aparecem aqui.</p>
            <a class="btn btn-cheio" href="produtos.php">Ver produtos</a>
        </div>

    <?php else: ?>

        <div class="carrinho-lista">
            <?php foreach ($carrinho as $id => $quantidade): ?>
                <?php
                $produto = $produtos[(string) $id] ?? null;
                if (!$produto) continue;

                $quantidade = (int) $quantidade;
                $subtotal   = (float) $produto['valor'] * $quantidade;
                $total     += $subtotal;
                ?>
                <article class="item-carrinho">
                    <a href="produto.php?id=<?= e((string) $id) ?>">
                        <img class="item-foto" src="<?= e(url_upload($produto['imagem'])) ?>" alt="<?= e($produto['nome']) ?>" loading="lazy">
                    </a>

                    <div class="item-info">
                        <h3><a href="produto.php?id=<?= e((string) $id) ?>"><?= e($produto['nome']) ?></a></h3>
                        <span class="preco-unit"><?= preco($produto['valor']) ?> cada</span>

                        <form method="post" class="qtd-controle">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= e((string) $id) ?>">

                            <button type="submit" name="acao" value="menos" aria-label="Diminuir">−</button>
                            <span class="qtd-num"><?= $quantidade ?></span>
                            <button type="submit" name="acao" value="mais" aria-label="Aumentar">+</button>

                            <button type="submit" name="acao" value="remover" class="btn-remover">Remover</button>
                        </form>
                    </div>

                    <div class="item-subtotal"><?= preco($subtotal) ?></div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="carrinho-total">
            <span>Total</span>
            <strong><?= preco($total) ?></strong>
        </div>

        <form method="post" class="form-pagar">
            <?= csrf_campo() ?>
            <a class="btn" href="produtos.php">Continuar comprando</a>
            <button type="submit" name="acao" value="pagar" class="btn btn-cheio">Finalizar compra</button>
        </form>

    <?php endif; ?>
</main>

<?php require __DIR__ . '/includes/footer.php';
