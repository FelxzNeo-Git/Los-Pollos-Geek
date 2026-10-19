<?php
/**
 * Configuração geral da loja + usuários.
 * As funções carregarUsuarios / salvarUsuarios / buscarUsuarioPorEmail / criarUsuario
 * guardam as contas em data/usuarios.json (não somem ao sair).
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/storage.php';


const LOJA_NOME     = 'Los Pollos Geek';
const LOJA_SLOGAN   = "One look, and you'll know.";
const LOJA_TELEFONE = '+55 11 90800-8022';
const LOJA_EMAIL    = 'Lospollosgeek@yahoo.com.br';


const LOJA_PUBLICA = false;

const ARQ_USUARIOS = DIR_DATA . '/usuarios.json';
function usuarioAdminPadrao(): array
{
    return [
        'id'    => 1,
        'nome'  => 'Administrador',
        'email' => 'admin@lospollosgeek.com',
        'senha' => password_hash('admin123', PASSWORD_DEFAULT),
        'admin' => true,
    ];
}

function carregarUsuarios(): array
{
    if (!is_file(ARQ_USUARIOS)) {
        json_atualizar(ARQ_USUARIOS, [], fn(array $u) => $u ?: [usuarioAdminPadrao()]);
    }
    return json_ler(ARQ_USUARIOS);
}

function salvarUsuarios(array $usuarios): bool
{
    return json_atualizar(ARQ_USUARIOS, [], fn() => array_values($usuarios));
}

function buscarUsuarioPorEmail(string $email): ?array
{
    foreach (carregarUsuarios() as $usuario) {
        if (mb_strtolower($usuario['email']) === mb_strtolower($email)) {
            return $usuario;
        }
    }
    return null;
}

function buscarUsuarioPorId(int $id): ?array
{
    foreach (carregarUsuarios() as $usuario) {
        if ((int) ($usuario['id'] ?? 0) === $id) {
            return $usuario;
        }
    }
    return null;
}
function criarUsuario(string $nome, string $email, string $senha, bool $localizacao = false): bool
{
    carregarUsuarios(); // garante o admin padrão antes de qualquer cadastro
    $criado = false;

    json_atualizar(ARQ_USUARIOS, [], function (array $usuarios) use ($nome, $email, $senha, $localizacao, &$criado) {
        $novoId = 1;
        foreach ($usuarios as $u) {
            if (mb_strtolower($u['email']) === mb_strtolower($email)) {
                return null;
            }
            $novoId = max($novoId, ($u['id'] ?? 0) + 1);
        }
        $usuarios[] = [
            'id'          => $novoId,
            'nome'        => $nome,
            'email'       => $email,
            'senha'       => password_hash($senha, PASSWORD_DEFAULT),
            'admin'       => false,
            'localizacao' => $localizacao,
        ];
        $criado = true;
        return $usuarios;
    });

    return $criado;
}
function atualizarLocalizacao(int $id, bool $permitir): bool
{
    return json_atualizar(ARQ_USUARIOS, [], function (array $usuarios) use ($id, $permitir) {
        foreach ($usuarios as &$u) {
            if ((int) ($u['id'] ?? 0) === $id) {
                $u['localizacao'] = $permitir;
            }
        }
        return $usuarios;
    });
}
