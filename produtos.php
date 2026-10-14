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

// Excluir Produto

if(isset($_POST['excluir'])) {

   $id = $_POST['id'];

    $sql = "DELETE FROM produtos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if  ($stmt->execute()) {
        echo "Produto excluído com sucesso!";
    } else {
        echo "Erro ao excluir produto.";
    }

    $stmt->close();
}

// Buscar produto para edição
   
if (isset($_POST['editar'])) {

    $id = $_POST['id'];

    $sql = "SELECT id, nome, autor, preco, quantidade
            FROM produtos WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado_edicao = $stmt->get_result();
    $produto_edicao = $resultado_edicao->fetch_assoc();

    $stmt->close();
}

// Atualizar produto
if (isset($_POST['atualizar'])) {

    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $autor = $_POST['autor'];
    $preco = $_POST['preco'];
    $quantidade = $_POST['quantidade'];

    $sql = "UPDATE produtos
            SET nome = ?, autor = ?, preco = ?, quantidade = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssdii",
        $nome,
        $autor,
        $preco,
        $quantidade,
        $id
    );

    if ($stmt->execute()) {
        echo "Produto alterado com sucesso!";
    } else {
        echo "Erro ao alterar produto.";
    }

    $stmt->close();
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


<?php if (isset($produto_edicao)) { ?>
    <h2>Editar Produto</h2>

    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $produto_edicao['id']; ?>">

        <label>Nome do Livro:</label>
        <input type="text" name="nome" value="<?php echo htmlspecialchars($produto_edicao['nome']); ?>" required>
        <br><br>

        <label>Autor:</label>
        <input type="text" name="autor" value="<?php echo htmlspecialchars($produto_edicao['autor']); ?>" required>
        <br><br>

        <label>Preço:</label>
        <input type="number" name="preco" step="0.01" min="0" value="<?php echo $produto_edicao['preco']; ?>" required>
        <br><br>

        <label>Quantidade:</label>
        <input type="number" name="quantidade" min="0" step="1" value="<?php echo $produto_edicao['quantidade']; ?>" required>
        <br><br>

        <button type="submit" name="atualizar">Salvar alterações</button>
    </form>
<?php } ?>


<h2>Produtos Cadastrados</h2>

<table border="1">

    <tr>
        <th>ID</th>
        <th>Nome do Livro</th>
        <th>Autor</th>
        <th>Preço</th>
        <th>Quantidade</th>
        <th>Ações</th>
    </tr>

    <?php while ($produto = $resultado->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $produto['id']; ?></td>
            <td><?php echo htmlspecialchars($produto['nome']); ?></td>
            <td><?php echo htmlspecialchars($produto['autor']); ?></td>
            <td><?php echo htmlspecialchars($produto['preco']); ?></td>
            <td><?php echo htmlspecialchars($produto['quantidade']); ?></td>
            
            <td>
    <form method="POST">
        <input type="hidden" name="id"
               value="<?php echo $produto['id']; ?>">

        <button type="submit" name="editar">
            Editar
        </button>


        <button type="submit" name="excluir"
                onclick="return confirm('Tem certeza que deseja excluir este produto?');">
            Excluir
         </button>

    </form>
</td>

        </tr>

        <?php } ?>

</table>
    
</body>
</html>
