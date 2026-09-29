<?php

// Informações de acesso ao banco de dados

$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "sistema_crud";

// Criação da conexão com o banco de dados

$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verificando conexão

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);


}

echo "Conexão realizada com sucesso!";

?>