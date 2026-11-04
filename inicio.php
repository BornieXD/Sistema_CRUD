
<?php

session_start();

// Se o administrador já estiver logado,
// direciona para o gerenciamento de livros.
if (isset($_SESSION["usuario_id"])) {
    header("Location: produtos.php");
    exit;
}

// Se o cliente já estiver logado,
// direciona para o catálogo.
if (isset($_SESSION["cliente_id"])) {
    header("Location: catalogo.php");
    exit;
}

require_once "conexão.php";

// Verifica se existe algum administrador cadastrado.
$sql = "SELECT COUNT(*) AS total FROM usuarios";
$resultado = $conn->query($sql);
$dados = $resultado->fetch_assoc();

$destino_admin = ($dados["total"] == 0)
    ? "cadastro_inicial.php"
    : "login.php";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/style.css">

    <title>Livraria - Página Inicial</title>
</head>

<body>

    <h1>Bem-vindo à Livraria!</h1>

    <h2>Escolha como deseja acessar o sistema</h2>

    <div class="acessos">

        <div class="acesso-card">

            <h2>Área Administrativa</h2>

            <p>
                Acesso destinado aos administradores
                responsáveis pelo gerenciamento da livraria.
            </p>

            <a href="<?php echo $destino_admin; ?>">
                Acessar ADM
            </a>

        </div>

        <div class="acesso-card">

            <h2>Área do Cliente</h2>

            <p>
                Acesse o catálogo de livros, consulte preços
                e verifique a disponibilidade.
            </p>

            <a href="login_cliente.php">
                Acessar loja
            </a>

        </div>

    </div>

</body>
</html>
