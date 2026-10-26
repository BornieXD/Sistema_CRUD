
<?php

session_start();

require_once "conexão.php";

// Verifica se já existe algum usuário
$sql = "SELECT COUNT(*) AS total FROM usuarios";
$resultado = $conn->query($sql);
$dados = $resultado->fetch_assoc();

// Se já houver usuário, não permite o cadastro inicial
if ($dados["total"] > 0) {
    header("Location: login.php");
    exit;
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];

    if ($nome != "" && $email != "" && $senha != "") {

        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nome, email, senha)
                VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nome, $email, $senha_hash);

        if ($stmt->execute()) {
            header("Location: login.php?cadastro=sucesso");
            exit;
        } else {
            $mensagem = "Não foi possível cadastrar. Verifique os dados.";
        }

        $stmt->close();

    } else {
        $mensagem = "Preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Cadastro Inicial - Sistema CRUD</title>
</head>
<body>

    <h1>Cadastro do Administrador</h1>

    <?php if ($mensagem != "") { ?>
        <p><?php echo htmlspecialchars($mensagem); ?></p>
    <?php } ?>

    <form method="POST" action="cadastro_inicial.php">

        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>
        <br><br>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>
        <br><br>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>
        <br><br>

        <button type="submit">Cadastrar</button>

    </form>

</body>
</html>
