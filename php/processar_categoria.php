<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_admin();
csrf_validar();

$dados = [
    'nome'      => trim($_POST['nome'] ?? ''),
    'descricao' => trim($_POST['descricao'] ?? ''),
];

$erros = [];
if (mb_strlen($dados['nome']) < 2 || mb_strlen($dados['nome']) > 60) {
    $erros[] = 'Informe o nome da categoria (2 a 60 caracteres).';
}
if (mb_strlen($dados['descricao']) > 200) {
    $erros[] = 'A descrição pode ter no máximo 200 caracteres.';
}

$imagem = salvar_imagem($_FILES['imagem'] ?? null, false, $erroImg);
if ($erroImg) {
    $erros[] = $erroImg;
}

if ($erros) {
    apagar_upload($imagem);
    flash('erros', $erros);
    flash('antigo', $dados);
    ir('nova_categoria.php');
}

$novoSlug = null;
$ok = json_atualizar(ARQ_CATALOGO, CATALOGO_BASE, function (array $cat) use ($dados, $imagem, &$novoSlug) {
    $base  = slug($dados['nome']);
    $slugs = array_column($cat['categorias'], 'slug');
    $novoSlug = $base;
    for ($n = 2; in_array($novoSlug, $slugs, true); $n++) {
        $novoSlug = $base . '-' . $n;
    }
    $cat['categorias'][] = $dados + [
        'id'     => bin2hex(random_bytes(6)),
        'slug'   => $novoSlug,
        'imagem' => $imagem,
        'criado' => time(),
    ];
    return $cat;
});

if (!$ok) {
    apagar_upload($imagem);
    flash('erros', ['Não foi possível gravar em data/. Verifique as permissões da pasta.']);
    flash('antigo', $dados);
    ir('nova_categoria.php');
}

flash('aviso', 'Categoria "' . $dados['nome'] . '" criada.');
ir('categoria.php?c=' . rawurlencode($novoSlug));
