
<?php

session_start();

// Permite o acesso somente a clientes autenticados
if (!isset($_SESSION["cliente_id"])) {
    header("Location: login_cliente.php");
    exit;
}

require_once "conexão.php";

// Busca os livros cadastrados pelo administrador
$sql = "SELECT id, nome, autor, preco, quantidade FROM produtos ORDER BY nome";
$resultado = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/style.css">

    <title>Catálogo de Livros</title>
</head>

<body>

    <h1>Catálogo de Livros</h1>

    <p>
        Bem-vindo(a),
        <strong>
            <?php echo htmlspecialchars($_SESSION["cliente_nome"], ENT_QUOTES, "UTF-8"); ?>
        </strong>!
    </p>

    <p>Confira os livros disponíveis em nossa livraria.</p>

    <form method="GET" action="catalogo.php">
        <label for="pesquisa">Pesquisar por título ou autor:</label>

        <input
            type="text"
            id="pesquisa"
            name="pesquisa"
            placeholder="Digite o título ou autor"
            value="<?php echo htmlspecialchars($_GET["pesquisa"] ?? "", ENT_QUOTES, "UTF-8"); ?>"
        >

        <button type="submit">Pesquisar</button>
    </form>

    <br>

    <?php
    // Se houver pesquisa, busca por título ou autor
    if (!empty(trim($_GET["pesquisa"] ?? ""))) {

        $pesquisa = "%" . trim($_GET["pesquisa"]) . "%";

        $sql = "SELECT id, nome, autor, preco, quantidade
                FROM produtos
                WHERE nome LIKE ? OR autor LIKE ?
                ORDER BY nome";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $pesquisa, $pesquisa);
        $stmt->execute();

        $resultado = $stmt->get_result();
    }
    ?>

    <?php if ($resultado && $resultado->num_rows > 0) { ?>

        <table border="1">

            <tr>
                <th>Título</th>
                <th>Autor</th>
                <th>Preço</th>
                <th>Disponibilidade</th>
            </tr>

            <?php while ($livro = $resultado->fetch_assoc()) { ?>

                <tr>
                    <td>
                        <?php echo htmlspecialchars($livro["nome"], ENT_QUOTES, "UTF-8"); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($livro["autor"], ENT_QUOTES, "UTF-8"); ?>
                    </td>

                    <td>
                        R$ <?php echo number_format((float)$livro["preco"], 2, ",", "."); ?>
                    </td>

                    <td>
                        <?php
                        if ($livro["quantidade"] > 0) {
                            echo "Disponível";
                        } else {
                            echo "Indisponível";
                        }
                        ?>
                    </td>
                </tr>

            <?php } ?>

        </table>

    <?php } else { ?>

        <p>Nenhum livro encontrado.</p>

    <?php } ?>

    <br>

    <a href="logout_cliente.php">Sair da conta</a>

</body>
</html>
