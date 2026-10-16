<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($email === '' || $senha === '') {
    $_SESSION['erro'] = 'Preencha e-mail e senha.';
    header('Location: login.php');
    exit;
}

$usuario = buscarUsuarioPorEmail($email);

if ($usuario && password_verify($senha, $usuario['senha'])) {
    session_regenerate_id(true); // evita fixação de sessão
    $_SESSION['usuario_id']    = $usuario['id'];
    $_SESSION['usuario_nome']  = $usuario['nome'];
    $_SESSION['usuario_admin'] = !empty($usuario['admin']);

    header('Location: ../index.php');
    exit;
}

$_SESSION['erro'] = 'E-mail ou senha inválidos.';
header('Location: login.php');
exit;
