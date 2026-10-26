<?php
session_start();

require_once "conexão.php";

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];

    $sql = "SELECT id, nome, email, senha FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if($resultado->num_rows == 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($senha, $usuario["senha"])) {
            session_regenerate_id(true);

            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["usuario_nome"] = $usuario["nome"];

            header("Location: produtos.php");
            exit();
        }
    }


     $mensagem = "Email ou senha inválidos.";  
          
            
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema CRUD</title>
</head>
<body>

    <h1>Login do Sistema</h1>

    <?php if (!empty($mensagem)) : ?>
        <p style="color: red;">
            <?= htmlspecialchars($mensagem) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label for="email">E-mail:</label>
        <input type="email" name="email" id="email" required>
        <br><br>

        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" required>
        <br><br>

        <button type="submit">Entrar</button>
    </form>
    
</body>
</html>
