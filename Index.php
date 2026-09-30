<?php

include 'conexão.php';


// Pega os valores informados pelo usuário no formulário

if (isset($_POST['cadastrar'])){
    
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Cadastra usuário no banco de dados, utilizando proteção de senha (CREATE)

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nome, $email, $senha_hash);

    if ($stmt->execute()) {
        echo "Usuário cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar usuário. Por favor, verifique as informações registradas e tente novamente!";
    }

    $stmt->close();
}

// Alterar cadastro de usuário 

if (isset($_POST['atualizar'])) {

    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];

    $sql = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nome, $email, $id);

    if ($stmt->execute()) {
        echo "Usuário alterado com sucesso!";
    } else {
        echo "Erro ao alterar usuário.";
    }

    $stmt->close();
}

$sql_usuarios = "SELECT id, nome, email FROM usuarios";
$resultado = $conn->query($sql_usuarios);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema CRUD</title>
</head>
<body>


<!--Formulário de cadastro de usuários-->

<h1> Cadastro de Usuários</h1>

<form method="POST">
    <label>Nome:</label>
    <input type="text" name="nome" required>
    <br><br>

    <label>Email:</label>
    <input type="email" name="email" required>
    <br><br>

    <label>Senha:</label>
    <input type ="password" name ="senha" required>
    <br><br>

    <button type="submit" name="cadastrar">Cadastrar</button>
    
</form>



<!-- Lista de usuários cadastrados (READ) -->

<h2>Usuários cadastrados</h2>

<table border="1">

    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Email</th>
    </tr>

    <?php while ($usuarios = $resultado->fetch_assoc()) { ?>

        <tr>
            <td><?php echo $usuarios['id']; ?></td>
            <td><?php echo $usuarios['nome']; ?></td>
            <td><?php echo $usuarios['email']; ?></td>
        </tr>

    <?php } ?>

</table>
    
</body>
</html>