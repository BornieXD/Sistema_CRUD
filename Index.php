
<?php
session_start();

// Se o administrador estiver logado
if (isset($_SESSION['usuario_id'])) {
    header('Location: produtos.php');
    exit;
}

// Se o cliente estiver logado
if (isset($_SESSION['cliente_id'])) {
    header('Location: catalogo.php');
    exit;
}

// Se ninguém estiver logado, abre a página inicial
header('Location: inicio.php');
exit;
