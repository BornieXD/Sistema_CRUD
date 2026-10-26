
<?php

require_once "conexão.php";

$mensagem = "";
$tipo_mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];
    $confirmar_senha = $_POST["confirmar_senha"];

    if ($nome == "" || $email == "" || $senha == "" || $confirmar_senha == "") {

        $mensagem = "Preencha todos os campos.";
        $tipo_mensagem = "erro";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensagem = "Informe um e-mail válido.";
        $tipo_mensagem = "erro";

    } elseif ($senha !== $confirmar_senha) {

        $mensagem = "As senhas não são iguais.";
        $tipo_mensagem = "erro";

    } else {

        // Verifica se o e-mail já está cadastrado
        $sql = "SELECT id FROM clientes WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {

            $mensagem = "Este e-mail já está cadastrado.";
            $tipo_mensagem = "erro";

        } else {

            // Protege a senha antes de salvar
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO clientes (nome, email, senha)
                    VALUES (?, ?, ?)";

            $stmt_insert = $conn->prepare($sql);
            $stmt_insert->bind_param("sss", $nome, $email, $senha_hash);

            if ($stmt_insert->execute()) {

                header("Location: login_cliente.php?cadastro=sucesso");
                exit;

            } else {

                $mensagem = "Não foi possível realizar o cadastro.";
                $tipo_mensagem = "erro";
            }

            $stmt_insert->close();
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/style.css">

    <title>Cadastro de Cliente</title>
</head>

<body>

    <h1>Cadastro de Cliente</h1>

    <?php if ($mensagem != "") { ?>
        <div class="notificacao <?php echo $tipo_mensagem; ?>">
            <?php echo htmlspecialchars($mensagem, ENT_QUOTES, "UTF-8"); ?>
        </div>
    <?php } ?>

    <form method="POST" action="cadastro_cliente.php">

        <label for="nome">Nome:</label>
        <input
            type="text"
            id="nome"
            name="nome"
            maxlength="100"
            required
        >
        <br><br>

        <label for="email">E-mail:</label>
        <input
            type="email"
            id="email"
            name="email"
            maxlength="150"
            required
        >
        <br><br>

        <label for="senha">Senha:</label>
        <input
            type="password"
            id="senha"
            name="senha"
            required
        >
        <br><br>

        <label for="confirmar_senha">Confirmar senha:</label>
        <input
            type="password"
            id="confirmar_senha"
            name="confirmar_senha"
            required
        >
        <br><br>

        <button type="submit">Cadastrar</button>

    </form>

    <p>
        Já possui uma conta?
        <a href="login_cliente.php">Entrar</a>
    </p>

</body>
</html>
