<?php

include 'conexão.php';


// Pega os valores informados pelo usuário no formulário

if (isset($_POST['cadastrar'])){
    
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
}

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
    
</body>
</html>