
<?php

session_start();

require_once "conexão.php";


if (isset($_SESSION["usuario_id"])) {
    header("Location: produtos.php");
    exit;
}


$sql = "SELECT COUNT(*) AS total FROM usuarios";
$resultado = $conn->query($sql);
$dados = $resultado->fetch_assoc();


if ($dados["total"] == 0) {
    header("Location: cadastro_inicial.php");
} else {
    header("Location: login.php");
}

exit;
?>
