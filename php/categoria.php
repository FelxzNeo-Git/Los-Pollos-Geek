<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_acesso();

$categoriaAtual = categoria_por_slug((string) ($_GET['c'] ?? ''));
if (!$categoriaAtual) {
    ir('produtos.php');
}
require __DIR__ . '/includes/listagem.php';
