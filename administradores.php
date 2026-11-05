<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'conexao.php';
require_once 'includes/seguranca.php';

$mensagem = '';
$tipoMensagem = 'sucesso';
$adminEdicao = null;
$tokenCsrf = gerarTokenCsrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarTokenCsrf();

    if (isset($_POST['cadastrar'])) {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($senha) < 8) {
            $mensagem = 'Informe nome, e-mail válido e senha com pelo menos 8 caracteres.';
            $tipoMensagem = 'erro';
        } else {
            $verifica = $conn->prepare('SELECT id FROM usuarios WHERE email = ?');
            $verifica->bind_param('s', $email);
            $verifica->execute();
            $existe = $verifica->get_result()->num_rows > 0;
            $verifica->close();

            if ($existe) {
                $mensagem = 'Este e-mail já está cadastrado.';
                $tipoMensagem = 'erro';
            } else {
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');
                $stmt->bind_param('sss', $nome, $email, $hash);
                if ($stmt->execute()) {
                    $mensagem = 'Administrador cadastrado com sucesso.';
                } else {
                    $mensagem = 'Não foi possível cadastrar o administrador.';
                    $tipoMensagem = 'erro';
                }
                $stmt->close();
            }
        }
    }

    if (isset($_POST['atualizar'])) {
        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $novaSenha = $_POST['senha'] ?? '';

        if (!$id || $nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensagem = 'Confira os dados informados.';
            $tipoMensagem = 'erro';
        } else {
            $verifica = $conn->prepare('SELECT id FROM usuarios WHERE email = ? AND id <> ?');
            $verifica->bind_param('si', $email, $id);
            $verifica->execute();
            $emailDuplicado = $verifica->get_result()->num_rows > 0;
            $verifica->close();

            if ($emailDuplicado) {
                $mensagem = 'Outro administrador já utiliza esse e-mail.';
                $tipoMensagem = 'erro';
            } elseif ($novaSenha !== '' && strlen($novaSenha) < 8) {
                $mensagem = 'A nova senha deve ter pelo menos 8 caracteres.';
                $tipoMensagem = 'erro';
            } else {
                if ($novaSenha !== '') {
                    $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare('UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?');
                    $stmt->bind_param('sssi', $nome, $email, $hash, $id);
                } else {
                    $stmt = $conn->prepare('UPDATE usuarios SET nome = ?, email = ? WHERE id = ?');
                    $stmt->bind_param('ssi', $nome, $email, $id);
                }

                if ($stmt->execute() && $stmt->affected_rows >= 0) {
                    if ((int)$id === (int)$_SESSION['usuario_id']) {
                        $_SESSION['usuario_nome'] = $nome;
                    }
                    $mensagem = 'Administrador atualizado com sucesso.';
                } else {
                    $mensagem = 'Não foi possível atualizar o administrador.';
                    $tipoMensagem = 'erro';
                }
                $stmt->close();
            }
        }
    }

    if (isset($_POST['excluir'])) {
        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        if (!$id) {
            $mensagem = 'Administrador inválido.';
            $tipoMensagem = 'erro';
        } elseif ((int)$id === (int)$_SESSION['usuario_id']) {
            $mensagem = 'Você não pode excluir a própria conta enquanto estiver conectado.';
            $tipoMensagem = 'erro';
        } else {
            $total = (int)$conn->query('SELECT COUNT(*) AS total FROM usuarios')->fetch_assoc()['total'];
            if ($total <= 1) {
                $mensagem = 'O sistema precisa manter pelo menos um administrador.';
                $tipoMensagem = 'erro';
            } else {
                $stmt = $conn->prepare('DELETE FROM usuarios WHERE id = ?');
                $stmt->bind_param('i', $id);
                if ($stmt->execute() && $stmt->affected_rows === 1) {
                    $mensagem = 'Administrador excluído com sucesso.';
                } else {
                    $mensagem = 'Não foi possível excluir o administrador.';
                    $tipoMensagem = 'erro';
                }
                $stmt->close();
            }
        }
    }
}

if (isset($_GET['editar'])) {
    $id = filter_var($_GET['editar'], FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $conn->prepare('SELECT id, nome, email FROM usuarios WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $adminEdicao = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}

$resultado = $conn->query('SELECT id, nome, email FROM usuarios ORDER BY nome');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Administradores | Livraria</title>
</head>
<body>
<header class="topo-pagina">
    <div><span class="marca">Livraria</span><span class="submarca">Painel administrativo</span></div>
    <nav class="navegacao">
        <a href="produtos.php">Livros</a>
        <a class="ativo" href="administradores.php">Administradores</a>
        <a class="sair" href="logout.php">Sair</a>
    </nav>
</header>
<main class="container">
    <div class="titulo-pagina"><div><p class="sobretitulo">GESTÃO DE ACESSO</p><h1>Administradores</h1><p>Cadastre e gerencie as contas com acesso ao painel.</p></div></div>
    <?php if ($mensagem !== ''): ?><div class="notificacao <?php echo escapar($tipoMensagem); ?>"><?php echo escapar($mensagem); ?></div><?php endif; ?>

    <section class="painel">
        <h2><?php echo $adminEdicao ? 'Editar administrador' : 'Cadastrar administrador'; ?></h2>
        <form method="POST" class="formulario">
            <input type="hidden" name="csrf_token" value="<?php echo escapar($tokenCsrf); ?>">
            <?php if ($adminEdicao): ?><input type="hidden" name="id" value="<?php echo (int)$adminEdicao['id']; ?>"><?php endif; ?>
            <label for="nome">Nome</label>
            <input id="nome" name="nome" type="text" maxlength="100" required value="<?php echo escapar($adminEdicao['nome'] ?? ''); ?>">
            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" maxlength="100" required value="<?php echo escapar($adminEdicao['email'] ?? ''); ?>">
            <label for="senha">Senha <?php echo $adminEdicao ? '(deixe em branco para manter a atual)' : ''; ?></label>
            <input id="senha" name="senha" type="password" <?php echo $adminEdicao ? '' : 'required'; ?> minlength="8" autocomplete="new-password" placeholder="Mínimo de 8 caracteres">
            <div class="linha-botoes">
                <button type="submit" name="<?php echo $adminEdicao ? 'atualizar' : 'cadastrar'; ?>"><?php echo $adminEdicao ? 'Salvar alterações' : 'Cadastrar administrador'; ?></button>
                <?php if ($adminEdicao): ?><a class="botao-secundario" href="administradores.php">Cancelar</a><?php endif; ?>
            </div>
        </form>
    </section>

    <section class="painel">
        <div class="cabecalho-secao"><div><h2>Administradores cadastrados</h2><p>O administrador conectado não pode excluir a própria conta.</p></div><span class="contador"><?php echo $resultado->num_rows; ?> conta(s)</span></div>
        <div class="tabela-responsiva"><table>
            <thead><tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Ações</th></tr></thead>
            <tbody>
            <?php while ($admin = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo (int)$admin['id']; ?></td>
                    <td><?php echo escapar($admin['nome']); ?><?php if ((int)$admin['id'] === (int)$_SESSION['usuario_id']): ?> <span class="etiqueta">Você</span><?php endif; ?></td>
                    <td><?php echo escapar($admin['email']); ?></td>
                    <td><div class="acoes">
                        <a class="botao-pequeno" href="administradores.php?editar=<?php echo (int)$admin['id']; ?>">Editar</a>
                        <?php if ((int)$admin['id'] !== (int)$_SESSION['usuario_id']): ?>
                        <form method="POST" class="form-acao" onsubmit="return confirm('Tem certeza que deseja excluir este administrador?');">
                            <input type="hidden" name="csrf_token" value="<?php echo escapar($tokenCsrf); ?>">
                            <input type="hidden" name="id" value="<?php echo (int)$admin['id']; ?>">
                            <button class="botao-perigo" type="submit" name="excluir">Excluir</button>
                        </form>
                        <?php else: ?><span class="texto-ajuda">Conta atual</span><?php endif; ?>
                    </div></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table></div>
    </section>
</main>
<script>
document.querySelectorAll('.notificacao').forEach((el) => setTimeout(() => el.classList.add('saindo'), 3500));
</script>
</body>
</html>
