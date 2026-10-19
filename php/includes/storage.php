<?php


if (!defined('RAIZ')) {
    define('RAIZ', dirname(__DIR__, 2));
}
const DIR_DATA    = RAIZ . '/data';
const DIR_UPLOADS = RAIZ . '/uploads';
function json_ler(string $arquivo, array $padrao = []): array
{
    if (!is_file($arquivo) || !($fp = fopen($arquivo, 'rb'))) {
        return $padrao;
    }
    flock($fp, LOCK_SH);
    $texto = stream_get_contents($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    $dados = json_decode($texto ?: '', true);
    return is_array($dados) ? $dados : $padrao;
}


function json_atualizar(string $arquivo, array $padrao, callable $alterar): bool
{
    if (!is_dir(dirname($arquivo)) && !mkdir(dirname($arquivo), 0755, true)) {
        return false;
    }
    if (!($fp = fopen($arquivo, 'c+b'))) {
        return false;
    }
    flock($fp, LOCK_EX);

    $dados = json_decode(stream_get_contents($fp) ?: '', true);
    $novo  = $alterar(is_array($dados) ? $dados : $padrao);

    $ok = false;
    if (is_array($novo)) {
        ftruncate($fp, 0);
        rewind($fp);
        $ok = fwrite($fp, json_encode($novo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) !== false;
        fflush($fp);
    }
    flock($fp, LOCK_UN);
    fclose($fp);
    return $ok;
}
