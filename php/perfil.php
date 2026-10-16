<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

$usuario = buscarUsuarioPorId((int) $_SESSION['usuario_id']);
if (!$usuario) {
    // Conta apagada: encerra a sessão
    header('Location: ../index.php?logout');
    exit;
}

// Alterar a permissão de localização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    atualizarLocalizacao((int) $usuario['id'], ($_POST['permitir_localizacao'] ?? '') === 'sim');
    flash('aviso', 'Preferência de localização atualizada.');
    ir('perfil.php');
}

$nome    = $usuario['nome']  ?? '';
$email   = $usuario['email'] ?? '';
$inicial = mb_strtoupper(mb_substr($nome, 0, 1));
$tipo    = !empty($usuario['admin']) ? 'Administrador' : 'Cliente';
$permitida = !empty($usuario['localizacao']);

if (!array_key_exists('localizacao', $usuario)) {
    $localizacao = 'Não informado';
} else {
    $localizacao = $permitida ? 'Permitida' : 'Não permitida';
}

$dados = [
    'Nome'                   => $nome,
    'E-mail'                 => $email,
    'Localização para envio' => $localizacao,
    'Tipo de conta'          => $tipo,
];

$titulo = 'Meu perfil';
require __DIR__ . '/includes/header.php';
?>

<main class="pagina pagina-estreita">
    <h1>Olá, <?= e(explode(' ', trim($nome))[0]) ?></h1>
    <p class="texto-suave">Confira os dados da sua conta.</p>

    <section class="perfil-card">
        <div class="perfil-avatar"><?= e($inicial) ?></div>
        <dl class="perfil-dados">
            <?php foreach ($dados as $rotulo => $valor): ?>
                <div class="perfil-linha">
                    <dt><?= e($rotulo) ?></dt>
                    <dd><?= e($valor) ?></dd>
                </div>
            <?php endforeach; ?>
        </dl>
    </section>

    <form class="perfil-local" method="post">
        <?= csrf_campo() ?>
        <p><strong>Localização para envio</strong></p>
        <label><input type="radio" name="permitir_localizacao" value="sim" <?= $permitida ? 'checked' : '' ?>> Permitir</label>
        <label><input type="radio" name="permitir_localizacao" value="nao" <?= !$permitida ? 'checked' : '' ?>> Não permitir</label>
        <button class="btn" type="submit">Salvar</button>
    </form>

    <p class="perfil-acoes">
        <a class="btn btn-cheio" href="produtos.php">Voltar à loja</a>
        <a class="btn" href="../index.php?logout">Sair da conta</a>
    </p>
</main>

<?php require __DIR__ . '/includes/footer.php';
