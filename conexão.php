<?php

// Informações de acesso ao banco de dados

$servidor = "localhost";
$usuarios = "root";
$senha = "";
$banco = "sistema_crud";

// Criação da conexão com o banco de dados

$conn = new mysqli($servidor, $usuarios, $senha, $banco);

// Verificando conexão

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);


}

echo "Conexão realizada com sucesso!";

?>