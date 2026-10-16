<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_admin();
csrf_validar();

$nome      = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$catId     = trim($_POST['categoria'] ?? '');
$valor     = paraNumero($_POST['valor'] ?? '');
$valorAnt  = paraNumero($_POST['valor_antigo'] ?? ''); // opcional

$erros = [];
if (mb_strlen($nome) < 3 || mb_strlen($nome) > 80) {
    $erros[] = 'Informe o nome do produto (3 a 80 caracteres).';
}
if (mb_strlen($descricao) > 500) {
    $erros[] = 'A descrição pode ter no máximo 500 caracteres.';
}
$categoria = categoria_por_id($catId);
if (!$categoria) {
    $erros[] = 'Escolha uma categoria.';
}
if ($valor === null || $valor <= 0) {
    $erros[] = 'Informe um valor válido, maior que zero.';
}
if (($_POST['valor_antigo'] ?? '') !== '' && ($valorAnt === null || $valorAnt <= 0)) {
    $erros[] = 'O valor original informado é inválido.';
}
if ($valorAnt !== null && $valor !== null && $valorAnt <= $valor) {
    $erros[] = 'O valor original deve ser maior que o valor de venda.';
}

$imagem = salvar_imagem($_FILES['imagem'] ?? null, true, $erroImg);
if ($erroImg) {
    $erros[] = $erroImg;
}

if ($erros) {
    apagar_upload($imagem);
    flash('erros', $erros);
    flash('antigo', [
        'nome' => $nome, 'descricao' => $descricao, 'categoria' => $catId,
        'valor' => $_POST['valor'] ?? '', 'valor_antigo' => $_POST['valor_antigo'] ?? '',
    ]);
    ir('criar_produto.php');
}

$ok = json_atualizar(ARQ_CATALOGO, CATALOGO_BASE, function (array $cat) use ($nome, $descricao, $catId, $valor, $valorAnt, $imagem) {
    $cat['produtos'][] = [
        'id'           => bin2hex(random_bytes(6)),
        'categoria'    => $catId,
        'nome'         => $nome,
        'descricao'    => $descricao,
        'valor'        => $valor,
        'valor_antigo' => $valorAnt,
        'imagem'       => $imagem,
        'criado'       => time(),
    ];
    return $cat;
});

if (!$ok) {
    apagar_upload($imagem);
    flash('erros', ['Não foi possível gravar em data/. Verifique as permissões da pasta.']);
    ir('criar_produto.php');
}

flash('aviso', 'Produto "' . $nome . '" adicionado em ' . $categoria['nome'] . '.');
ir('categoria.php?c=' . rawurlencode($categoria['slug']));
