
<?php

session_start();

require_once "conexão.php";

$mensagem = "";

if (isset($_GET["cadastro"]) && $_GET["cadastro"] == "sucesso") {
    $mensagem = "Cadastro realizado com sucesso! Faça seu login.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];

    $sql = "SELECT id, nome, email, senha FROM clientes WHERE email = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 1) {

        $cliente = $resultado->fetch_assoc();

        if (password_verify($senha, $cliente["senha"])) {

            session_regenerate_id(true);

            $_SESSION["cliente_id"] = $cliente["id"];
            $_SESSION["cliente_nome"] = $cliente["nome"];

            header("Location: catalogo.php");
            exit;

        } else {
            $mensagem = "E-mail ou senha incorretos.";
        }

    } else {
        $mensagem = "E-mail ou senha incorretos.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Login do Cliente</title>
</head>
<body>

    <h1>Login do Cliente</h1>

    <?php if ($mensagem != "") { ?>
        <p><?php echo htmlspecialchars($mensagem, ENT_QUOTES, "UTF-8"); ?></p>
    <?php } ?>

    <form method="POST" action="login_cliente.php">

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>
        <br><br>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>
        <br><br>

        <button type="submit">Entrar</button>

    </form>

    <p>
        Ainda não possui conta?
        <a href="cadastro_cliente.php">Cadastre-se</a>
    </p>

</body>
</html>
