<?php
/**
 * Inicialização comum: sessão, helpers, autenticação, catálogo (categorias/produtos) e carrinho.
 *
 * Caminhos: as páginas dentro de /php usam asset() e pg() para montar links certos.
 * O index.php (na raiz) define NA_RAIZ antes de chamar este arquivo.
 */

require_once __DIR__ . '/../config.php';

const ARQ_CATALOGO = DIR_DATA . '/catalogo.json';

/** Categorias que já vêm prontas na primeira execução. O admin pode criar outras ou excluir estas. */
const CATALOGO_BASE = [
    'categorias' => [
        ['id' => 'action-figures', 'slug' => 'action-figures', 'nome' => 'Action Figures', 'descricao' => 'Bonecos articulados e estátuas de animes, games, filmes e quadrinhos.', 'imagem' => null, 'criado' => 1],
        ['id' => 'funko-pop', 'slug' => 'funko-pop', 'nome' => 'Funko Pop', 'descricao' => 'Os bonequinhos de cabeça grande dos seus personagens favoritos.', 'imagem' => null, 'criado' => 2],
        ['id' => 'camisetas', 'slug' => 'camisetas', 'nome' => 'Camisetas', 'descricao' => 'Camisetas estampadas com séries, filmes, animes e games.', 'imagem' => null, 'criado' => 3],
        ['id' => 'acessorios', 'slug' => 'acessorios', 'nome' => 'Acessórios', 'descricao' => 'Canecas, chaveiros, mochilas, luminárias e outros itens para o dia a dia.', 'imagem' => null, 'criado' => 4],
        ['id' => 'pokemon', 'slug' => 'pokemon', 'nome' => 'Pokémon', 'descricao' => 'Pelúcias, cartas, figures e tudo de Pokémon.', 'imagem' => null, 'criado' => 5],
    ],
    'produtos' => [],
];

/* ------------------------------------------------------------------ */
/*  Caminhos                                                           */
/* ------------------------------------------------------------------ */

/** Caminho de um arquivo da raiz do site (css, js, img, uploads) a partir da página atual. */
function asset(string $caminho): string
{
    return (defined('NA_RAIZ') ? '' : '../') . $caminho;
}

/** Caminho de uma página da pasta /php a partir da página atual. */
function pg(string $pagina): string
{
    return (defined('NA_RAIZ') ? 'php/' : '') . $pagina;
}

/** Página atual, no formato que o carrinho.php aceita para voltar depois de adicionar um item. */
function voltar_atual(): string
{
    if (defined('NA_RAIZ')) {
        return '../index.php';
    }
    $arquivo = basename(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: 'index.php');
    $query   = $_SERVER['QUERY_STRING'] ?? '';
    return $arquivo . ($query !== '' ? '?' . $query : '');
}

/* ------------------------------------------------------------------ */
/*  Helpers gerais                                                     */
/* ------------------------------------------------------------------ */
function e(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function preco(float $v): string
{
    return 'R$ ' . number_format($v, 2, ',', '.');
}

function ir(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/** Converte "4.890,00", "R$ 4890,00" ou "4890.00" em float (null se inválido). */
function paraNumero(string $v): ?float
{
    $v = trim(str_ireplace(['R$', ' '], '', $v));
    if ($v === '') {
        return null;
    }
    if (str_contains($v, ',')) {
        $v = str_replace(['.', ','], ['', '.'], $v);
    }
    return is_numeric($v) ? (float) $v : null;
}

function slug(string $texto): string
{
    $texto = strtr(mb_strtolower($texto), [
        'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i', 'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ç' => 'c', 'ñ' => 'n',
    ]);
    $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
    return trim($texto, '-') ?: 'item';
}

/* ------------------------------------------------------------------ */
/*  Flash (mensagens/dados antigos de formulário) e CSRF               */
/* ------------------------------------------------------------------ */
function flash(string $chave, mixed $valor = null): mixed
{
    if ($valor !== null) {
        $_SESSION['flash'][$chave] = $valor;
        return null;
    }
    $v = $_SESSION['flash'][$chave] ?? null;
    unset($_SESSION['flash'][$chave]);
    return $v;
}

function csrf_token(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_validar(): void
{
    $esperado = $_SESSION['csrf'] ?? '';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $esperado === ''
        || !hash_equals($esperado, (string) ($_POST['csrf'] ?? ''))) {
        http_response_code(400);
        exit('Requisição inválida. Volte e tente novamente.');
    }
}

/* ------------------------------------------------------------------ */
/*  Autenticação                                                       */
/* ------------------------------------------------------------------ */
function logado(): bool
{
    return !empty($_SESSION['usuario_id']);
}

function admin(): bool
{
    return logado() && !empty($_SESSION['usuario_admin']);
}

function exigir_login(): void
{
    if (!logado()) {
        ir(pg('login.php'));
    }
}

/** Páginas de navegação da loja: pedem login só se LOJA_PUBLICA for false. */
function exigir_acesso(): void
{
    if (!LOJA_PUBLICA) {
        exigir_login();
    }
}

function exigir_admin(): void
{
    exigir_login();
    if (!admin()) {
        http_response_code(403);
        exit('Acesso restrito a administradores. <a href="' . e(asset('index.php')) . '">Voltar</a>');
    }
}

/* ------------------------------------------------------------------ */
/*  Catálogo                                                           */
/* ------------------------------------------------------------------ */
function catalogo(): array
{
    return json_ler(ARQ_CATALOGO, CATALOGO_BASE);
}

function categorias(): array
{
    return catalogo()['categorias'];
}

function categoria_por_slug(string $slug): ?array
{
    foreach (categorias() as $c) {
        if ($c['slug'] === $slug) {
            return $c;
        }
    }
    return null;
}

function categoria_por_id(string $id): ?array
{
    foreach (categorias() as $c) {
        if ($c['id'] === $id) {
            return $c;
        }
    }
    return null;
}

function produto_por_id(string $id): ?array
{
    foreach (catalogo()['produtos'] as $p) {
        if ($p['id'] === $id) {
            return $p;
        }
    }
    return null;
}

function produtos_da_categoria(string $id): array
{
    return array_values(array_filter(catalogo()['produtos'], fn($p) => $p['categoria'] === $id));
}

function contar_produtos(string $categoriaId): int
{
    return count(produtos_da_categoria($categoriaId));
}

/** Produtos mais novos primeiro (opcionalmente só de uma categoria e/ou limitados). */
function produtos_recentes(?int $limite = null, ?string $categoriaId = null): array
{
    $lista = $categoriaId === null ? catalogo()['produtos'] : produtos_da_categoria($categoriaId);
    usort($lista, fn($a, $b) => ($b['criado'] ?? 0) <=> ($a['criado'] ?? 0));
    return $limite ? array_slice($lista, 0, $limite) : $lista;
}

function nome_categoria(string $id): string
{
    static $mapa = null;
    $mapa ??= array_column(categorias(), 'nome', 'id');
    return $mapa[$id] ?? '';
}

/**
 * Imagem da categoria: a enviada pelo admin ou, se não houver, um arquivo em img/categorias/<slug>.png
 * (ex.: img/categorias/funko-pop.png). Sem nenhuma das duas, devolve null e a loja mostra só o nome.
 */
function imagem_categoria(array $cat): ?string
{
    if (!empty($cat['imagem'])) {
        return url_upload($cat['imagem']);
    }
    $arquivo = 'img/categorias/' . $cat['slug'] . '.png';
    return is_file(RAIZ . '/' . $arquivo) ? asset($arquivo) : null;
}

/** URL pública de um arquivo enviado. */
function url_upload(?string $arquivo): string
{
    return $arquivo ? asset('uploads/' . rawurlencode($arquivo)) : '';
}

function apagar_upload(?string $arquivo): void
{
    $caminho = DIR_UPLOADS . '/' . basename((string) $arquivo);
    if ($arquivo && is_file($caminho)) {
        unlink($caminho);
    }
}

/**
 * Valida e salva uma imagem enviada. Devolve o nome do arquivo salvo ou null (e preenche $erro).
 * Se $obrigatoria for false e nenhum arquivo for enviado, devolve null sem erro.
 */
function salvar_imagem(?array $arquivo, bool $obrigatoria, ?string &$erro): ?string
{
    $erro = null;
    $permitidas = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];

    if (!$arquivo || $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
        $erro = $obrigatoria ? 'Envie uma imagem.' : null;
        return null;
    }
    if ($arquivo['error'] === UPLOAD_ERR_INI_SIZE || $arquivo['error'] === UPLOAD_ERR_FORM_SIZE) {
        $erro = 'A imagem é grande demais (máximo 5 MB).';
        return null;
    }
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        $erro = 'Não foi possível enviar a imagem. Tente novamente.';
        return null;
    }
    if ($arquivo['size'] > 5 * 1024 * 1024) {
        $erro = 'A imagem deve ter no máximo 5 MB.';
        return null;
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
    if (!isset($permitidas[$mime]) || @getimagesize($arquivo['tmp_name']) === false) {
        $erro = 'O arquivo enviado não é uma imagem válida (use JPG, PNG, WEBP ou GIF).';
        return null;
    }
    if (!is_dir(DIR_UPLOADS) && !mkdir(DIR_UPLOADS, 0755, true)) {
        $erro = 'Pasta uploads/ inexistente e não pôde ser criada.';
        return null;
    }
    $nome = bin2hex(random_bytes(8)) . '.' . $permitidas[$mime];
    if (!move_uploaded_file($arquivo['tmp_name'], DIR_UPLOADS . '/' . $nome)) {
        $erro = 'Falha ao salvar a imagem. Verifique a permissão da pasta uploads/.';
        return null;
    }
    return $nome;
}

/* ------------------------------------------------------------------ */
/*  Carrinho (sessão)                                                  */
/* ------------------------------------------------------------------ */

/** Quantidade de itens no carrinho (ignora produtos que já foram excluídos). */
function carrinho_qtd(): int
{
    $ids = array_column(catalogo()['produtos'], 'id');
    $qtd = 0;
    foreach ($_SESSION['carrinho'] ?? [] as $id => $n) {
        if (in_array((string) $id, $ids, true)) {
            $qtd += (int) $n;
        }
    }
    return $qtd;
}

/* ------------------------------------------------------------------ */
/*  Componente: card de produto (usado na home, listagens e relacionados) */
/* ------------------------------------------------------------------ */
function card_produto(array $p): string
{
    $desconto = !empty($p['valor_antigo']) ? (int) round((1 - $p['valor'] / $p['valor_antigo']) * 100) : 0;
    $novo     = (time() - ($p['criado'] ?? 0)) < 30 * 86400;
    $link     = pg('produto.php') . '?id=' . rawurlencode($p['id']);

    ob_start(); ?>
    <article class="card" data-cat="<?= e($p['categoria']) ?>">
        <a class="card-foto" href="<?= e($link) ?>">
            <img src="<?= e(url_upload($p['imagem'])) ?>" alt="<?= e($p['nome']) ?>" loading="lazy">
            <?php if ($desconto > 0): ?><span class="selo selo-desc">-<?= $desconto ?>%</span>
            <?php elseif ($novo): ?><span class="selo selo-novo">Novo</span><?php endif; ?>
        </a>
        <div class="card-corpo">
            <p class="card-cat"><?= e(nome_categoria($p['categoria'])) ?></p>
            <h3 class="card-nome"><a href="<?= e($link) ?>"><?= e($p['nome']) ?></a></h3>
            <p class="card-preco">
                <?php if (!empty($p['valor_antigo'])): ?><s><?= preco($p['valor_antigo']) ?></s><?php endif; ?>
                <strong><?= preco($p['valor']) ?></strong>
            </p>
            <form method="post" action="<?= e(pg('carrinho.php')) ?>">
                <?= csrf_campo() ?>
                <input type="hidden" name="acao" value="mais">
                <input type="hidden" name="id" value="<?= e($p['id']) ?>">
                <input type="hidden" name="voltar" value="<?= e(voltar_atual()) ?>">
                <button class="btn btn-cheio" type="submit">Adicionar</button>
            </form>
        </div>
    </article>
    <?php
    return ob_get_clean();
}
