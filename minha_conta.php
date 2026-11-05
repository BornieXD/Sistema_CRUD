<?php
session_start();

if (!isset($_SESSION['cliente_id'])) {
    header('Location: login_cliente.php');
    exit;
}

require_once 'conexao.php';
require_once 'includes/seguranca.php';

$mensagem = '';
$tipoMensagem = 'sucesso';
$tokenCsrf = gerarTokenCsrf();
$clienteId = (int)$_SESSION['cliente_id'];

$stmt = $conn->prepare('SELECT id, nome, email, senha FROM clientes WHERE id = ?');
$stmt->bind_param('i', $clienteId);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cliente) {
    $_SESSION = [];
    session_destroy();
    header('Location: login_cliente.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarTokenCsrf();

    if (isset($_POST['atualizar'])) {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senhaAtual = $_POST['senha_atual'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';

        if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensagem = 'Informe um nome e um e-mail válido.';
            $tipoMensagem = 'erro';
        } elseif ($novaSenha !== '' && (!password_verify($senhaAtual, $cliente['senha']) || strlen($novaSenha) < 8 || $novaSenha !== $confirmarSenha)) {
            $mensagem = 'Para alterar a senha, informe a senha atual e uma nova senha válida (mínimo de 8 caracteres), repetida corretamente.';
            $tipoMensagem = 'erro';
        } else {
            $verifica = $conn->prepare('SELECT id FROM clientes WHERE email = ? AND id <> ?');
            $verifica->bind_param('si', $email, $clienteId);
            $verifica->execute();
            $duplicado = $verifica->get_result()->num_rows > 0;
            $verifica->close();

            if ($duplicado) {
                $mensagem = 'Este e-mail já está sendo utilizado por outra conta.';
                $tipoMensagem = 'erro';
            } else {
                if ($novaSenha !== '') {
                    $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
                    $update = $conn->prepare('UPDATE clientes SET nome = ?, email = ?, senha = ? WHERE id = ?');
                    $update->bind_param('sssi', $nome, $email, $hash, $clienteId);
                } else {
                    $update = $conn->prepare('UPDATE clientes SET nome = ?, email = ? WHERE id = ?');
                    $update->bind_param('ssi', $nome, $email, $clienteId);
                }

                if ($update->execute()) {
                    $_SESSION['cliente_nome'] = $nome;
                    $mensagem = 'Seus dados foram atualizados com sucesso.';
                    $cliente['nome'] = $nome;
                    $cliente['email'] = $email;
                    if ($novaSenha !== '') {
                        $cliente['senha'] = $hash;
                    }
                } else {
                    $mensagem = 'Não foi possível atualizar seus dados.';
                    $tipoMensagem = 'erro';
                }
                $update->close();
            }
        }
    }

    if (isset($_POST['excluir_conta'])) {
        $senhaConfirmacao = $_POST['senha_confirmacao'] ?? '';
        if (!password_verify($senhaConfirmacao, $cliente['senha'])) {
            $mensagem = 'Senha incorreta. A conta não foi excluída.';
            $tipoMensagem = 'erro';
        } else {
            $delete = $conn->prepare('DELETE FROM clientes WHERE id = ?');
            $delete->bind_param('i', $clienteId);
            if ($delete->execute() && $delete->affected_rows === 1) {
                $_SESSION = [];
                if (ini_get('session.use_cookies')) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
                }
                session_destroy();
                header('Location: inicio.php?conta=excluida');
                exit;
            }
            $mensagem = 'Não foi possível excluir a conta.';
            $tipoMensagem = 'erro';
            $delete->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Minha conta | Livraria</title>
</head>
<body>
<header class="topo-pagina">
    <div><span class="marca">Livraria</span><span class="submarca">Área do cliente</span></div>
    <nav class="navegacao"><a href="catalogo.php">Catálogo</a><a class="ativo" href="minha_conta.php">Minha conta</a><a class="sair" href="logout_cliente.php">Sair</a></nav>
</header>
<main class="container container-menor">
    <div class="titulo-pagina"><div><p class="sobretitulo">SEUS DADOS</p><h1>Minha conta</h1><p>Atualize suas informações ou encerre sua conta.</p></div></div>
    <?php if ($mensagem !== ''): ?><div class="notificacao <?php echo escapar($tipoMensagem); ?>"><?php echo escapar($mensagem); ?></div><?php endif; ?>
    <section class="painel">
        <h2>Editar cadastro</h2>
        <form method="POST" class="formulario">
            <input type="hidden" name="csrf_token" value="<?php echo escapar($tokenCsrf); ?>">
            <label for="nome">Nome</label><input id="nome" name="nome" type="text" maxlength="100" required value="<?php echo escapar($cliente['nome']); ?>">
            <label for="email">E-mail</label><input id="email" name="email" type="email" maxlength="150" required value="<?php echo escapar($cliente['email']); ?>">
            <div class="separador-form"><strong>Alterar senha (opcional)</strong><span>Preencha os três campos abaixo somente se desejar trocar sua senha.</span></div>
            <label for="senha_atual">Senha atual</label><input id="senha_atual" name="senha_atual" type="password" autocomplete="current-password">
            <label for="nova_senha">Nova senha</label><input id="nova_senha" name="nova_senha" type="password" minlength="8" autocomplete="new-password">
            <label for="confirmar_senha">Confirmar nova senha</label><input id="confirmar_senha" name="confirmar_senha" type="password" minlength="8" autocomplete="new-password">
            <button type="submit" name="atualizar">Salvar alterações</button>
        </form>
    </section>
    <section class="painel painel-perigo">
        <h2>Excluir minha conta</h2>
        <p>Essa ação é permanente. Seus dados de cadastro serão removidos e você será desconectado.</p>
        <form method="POST" class="formulario" onsubmit="return confirm('A exclusão da conta é permanente. Deseja continuar?');">
            <input type="hidden" name="csrf_token" value="<?php echo escapar($tokenCsrf); ?>">
            <label for="senha_confirmacao">Confirme sua senha para excluir a conta</label>
            <input id="senha_confirmacao" name="senha_confirmacao" type="password" required autocomplete="current-password">
            <button class="botao-perigo" type="submit" name="excluir_conta">Excluir minha conta</button>
        </form>
    </section>
</main>
<script>document.querySelectorAll('.notificacao').forEach((el) => setTimeout(() => el.classList.add('saindo'), 3500));</script>
</body>
</html>
