<?php

include 'conexão.php';

// Cadastrar produto
if (isset($_POST['cadastrar'])) {

    $nome = $_POST['nome'];
    $autor = $_POST['autor'];
    $preco = $_POST['preco'];
    $quantidade = $_POST['quantidade'];

    $sql = "INSERT INTO produtos (nome, autor, preco, quantidade) VALUES (?, ?, ?, ?)";

    $stm = $conn->prepare($sql);

    $stm->bind_param("ssdi", $nome, $autor, $preco, $quantidade);

    if($stm->execute()) {
        echo "Produto cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar produto.!";
    }

    $stm->close();
}

// Listar produtos

$sql_produtos = "SELECT id, nome, autor, preco, quantidade FROM produtos";

$resultado = $conn->query($sql_produtos);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Livraria</title>
</head>
<body>
    
     <h1>Cadastro de Produtos</h1>

     <form method="POST">

     <label>Nome do Livro</label>
     <input type="text" name="nome" required>

     <br><br>

     <label>Autor</label>
        <input type="text" name="autor" required>

        <br><br>

        <label>Preço</label>
        <input type="text" name="preco" required>

        <br><br>

        <label>Quantidade</label>
        <input type="text" name="quantidade" required>

        <br><br>

        <button type="submit" name="cadastrar">
            Cadastrar Produto
        </button>

</form>

<h2>Produtos Cadastrados</h2>

<table border="1">

    <tr>
        <th>ID</th>
        <th>Nome do Livro</th>
        <th>Autor</th>
        <th>Preço</th>
        <th>Quantidade</th>
    </tr>

    <?php while ($produto = $resultado->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $produto['id']; ?></td>
            <td><?php echo htmlspecialchars($produto['nome']); ?></td>
            <td><?php echo htmlspecialchars($produto['autor']); ?></td>
            <td><?php echo htmlspecialchars($produto['preco']); ?></td>
            <td><?php echo htmlspecialchars($produto['quantidade']); ?></td>
        </tr>

        <?php } ?>

</table>
    
</body>
</html>
