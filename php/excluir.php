<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_admin();
csrf_validar();

$tipo = $_POST['tipo'] ?? '';
$id   = (string) ($_POST['id'] ?? '');
$imagensApagar = [];

json_atualizar(ARQ_CATALOGO, CATALOGO_BASE, function (array $cat) use ($tipo, $id, &$imagensApagar) {
    if ($tipo === 'categoria') {
        foreach ($cat['categorias'] as $c) {
            if ($c['id'] === $id) {
                $imagensApagar[] = $c['imagem'] ?? null;
            }
        }
        foreach ($cat['produtos'] as $p) {
            if ($p['categoria'] === $id) {
                $imagensApagar[] = $p['imagem'];
                unset($_SESSION['carrinho'][$p['id']]);
            }
        }
        $cat['categorias'] = array_values(array_filter($cat['categorias'], fn($c) => $c['id'] !== $id));
        $cat['produtos']   = array_values(array_filter($cat['produtos'], fn($p) => $p['categoria'] !== $id));
    } elseif ($tipo === 'produto') {
        foreach ($cat['produtos'] as $p) {
            if ($p['id'] === $id) {
                $imagensApagar[] = $p['imagem'];
            }
        }
        $cat['produtos'] = array_values(array_filter($cat['produtos'], fn($p) => $p['id'] !== $id));
        unset($_SESSION['carrinho'][$id]);
    } else {
        return null;
    }
    return $cat;
});

array_map('apagar_upload', $imagensApagar);
flash('aviso', $tipo === 'categoria' ? 'Categoria excluída.' : 'Produto excluído.');
ir('admin.php');
