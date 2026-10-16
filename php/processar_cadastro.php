<?php
require_once __DIR__ . '/config.php';

function voltarCadastro(string $mensagem): never
{
    $_SESSION['erro'] = $mensagem;
    header('Location: cadastro.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.php');
    exit;
}

$nome      = trim($_POST['nome'] ?? '');
$email     = trim($_POST['email'] ?? '');
$senha     = $_POST['senha'] ?? '';
$confirmar = $_POST['confirmar_senha'] ?? '';
$local     = ($_POST['permitir_localizacao'] ?? '') === 'sim';

if ($nome === '' || $email === '' || $senha === '' || $confirmar === '') {
    voltarCadastro('Preencha todos os campos.');
}
if (mb_strlen($nome) < 2 || mb_strlen($nome) > 100) {
    voltarCadastro('O nome deve ter entre 2 e 100 caracteres.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    voltarCadastro('E-mail inválido.');
}
if (strlen($senha) < 6) {
    voltarCadastro('A senha deve ter pelo menos 6 caracteres.');
}
if ($senha !== $confirmar) {
    voltarCadastro('As senhas não coincidem.');
}
if (!criarUsuario($nome, $email, $senha, $local)) {
    voltarCadastro('E-mail já cadastrado.');
}

$_SESSION['sucesso'] = 'Cadastro realizado! Você já pode entrar.';
header('Location: login.php');
exit;
